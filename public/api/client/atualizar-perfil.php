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
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Payload inválido.']);
    exit;
}

$id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
$nome = isset($input['nome']) ? trim($input['nome']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$telefone = isset($input['telefone']) ? trim($input['telefone']) : '';
$endereco = isset($input['endereco']) ? trim($input['endereco']) : '';

if ($id_cliente <= 0 || $nome === '' || $email === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dados obrigatórios ausentes.']);
    exit;
}

$sql = "UPDATE CLIENTE SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id_cliente = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao preparar consulta: '.$con->error]);
    exit;
}
$stmt->bind_param('ssssi', $nome, $email, $telefone, $endereco, $id_cliente);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao atualizar perfil: '.$stmt->error]);
    exit;
}
$stmt->close();
$con->close();

echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso.']);