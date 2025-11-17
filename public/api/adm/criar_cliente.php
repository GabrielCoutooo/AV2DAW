<?php
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!function_exists('adminEstaLogado') || !adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acesso negado. Faça login como administrador.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$nome     = trim($input['nome'] ?? '');
$email    = trim($input['email'] ?? '');
$cpf      = preg_replace('/\D+/', '', $input['cpf'] ?? '');
$telefone = trim($input['telefone'] ?? '');

if ($nome === '' || $email === '' || $cpf === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Informe nome, email e CPF.']);
    exit;
}

// Verifica se já existe por CPF ou e-mail (normaliza CPF para match)
$chk = $con->prepare("SELECT id_cliente FROM cliente WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ? OR email = ? LIMIT 1");
$chk->bind_param('ss', $cpf, $email);
$chk->execute();
$r = $chk->get_result()->fetch_assoc();
$chk->close();
if ($r) {
    http_response_code(409);
    echo json_encode(['success' => false, 'error' => 'Já existe um cliente com este CPF ou email.']);
    exit;
}

// Gera uma senha temporária e hash
$senhaTemp = bin2hex(random_bytes(4));
$hash = password_hash($senhaTemp, PASSWORD_BCRYPT);
$agora = date('Y-m-d H:i:s');

$stmt = $con->prepare("INSERT INTO cliente (nome, cpf, email, senha_hash, telefone, endereco, data_cadastro) VALUES (?, ?, ?, ?, ?, '', ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno (prepare): ' . $con->error]);
    exit;
}
$stmt->bind_param('ssssss', $nome, $cpf, $email, $hash, $telefone, $agora);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao inserir cliente: ' . $stmt->error]);
    $stmt->close();
    exit;
}
$idCliente = $stmt->insert_id;
$stmt->close();

// Retorna sem expor senha; admin pode instruir o cliente a usar "Esqueci minha senha" para definir senha

echo json_encode([
    'success' => true,
    'id_cliente' => $idCliente,
    'mensagem' => 'Cliente criado com sucesso. Oriente-o a definir a senha no login.'
]);
