<?php

header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!function_exists('clienteEstaLogado') || !clienteEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$current = $input['current_password'] ?? '';
$new = $input['new_password'] ?? '';

if (trim($current) === '' || trim($new) === '' || strlen($new) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Senha inválida. A nova senha deve ter ao menos 6 caracteres.']);
    exit;
}

$id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
if ($id_cliente <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID do cliente inválido.']);
    exit;
}

// busca senha atual (assumindo campo senha_hash na tabela cliente)
$sql = "SELECT senha_hash FROM cliente WHERE id_cliente = ? LIMIT 1";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $con->error]);
    exit;
}
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Perfil não encontrado.']);
    exit;
}

$stored = $row['senha_hash'] ?? '';

// Verifica hash — suporta hash produzido por password_hash
$ok = false;
if (password_verify($current, $stored)) {
    $ok = true;
} else {
    // fallback: se senha no BD for plain text (não recomendado), compare diretamente
    if ($current === $stored) $ok = true;
}

if (!$ok) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Senha atual incorreta.']);
    exit;
}

// atualiza com hash
$newHash = password_hash($new, PASSWORD_DEFAULT);
$upd = $con->prepare("UPDATE cliente SET senha_hash = ? WHERE id_cliente = ?");
if (!$upd) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $con->error]);
    exit;
}
$upd->bind_param('si', $newHash, $id_cliente);
if (!$upd->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao atualizar senha: ' . $upd->error]);
    exit;
}
$upd->close();
$con->close();

echo json_encode(['success' => true, 'message' => 'Senha atualizada com sucesso.']);
