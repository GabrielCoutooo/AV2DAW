
<?php
header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);

// inclui configuração, conexão e verificação de autenticação
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!function_exists('clienteEstaLogado') || !clienteEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
    exit;
}

$id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
if ($id_cliente <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID do cliente inválido.']);
    exit;
}

$sql = "SELECT nome, email, telefone, cpf, endereco FROM cliente WHERE id_cliente = ? LIMIT 1";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $con->error]);
    exit;
}
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
$perfil = $res->fetch_assoc();
$stmt->close();
$con->close();

if (!$perfil) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Perfil não encontrado.']);
    exit;
}

echo json_encode(['success' => true, 'perfil' => $perfil], JSON_UNESCAPED_UNICODE);
