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

$id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
if ($id_cliente <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID do cliente inválido.']);
    exit;
}

$sql = "SELECT L.id_locacao, L.data_hora_retirada, L.data_hora_prevista_devolucao, L.status, L.valor_total,
               V.id_veiculo, M.nome_modelo, M.marca, M.imagem /* <-- Adicionado M.imagem */
        FROM locacao L
        LEFT JOIN veiculo V ON L.id_veiculo = V.id_veiculo
        LEFT JOIN modelo M ON V.id_modelo = M.id_modelo
        WHERE L.id_cliente = ?
        ORDER BY L.data_hora_retirada DESC";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $con->error]);
    exit;
}
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
$locacoes = [];
while ($row = $res->fetch_assoc()) {
    $locacoes[] = $row;
}
$stmt->close();
$con->close();

echo json_encode(['success' => true, 'locacoes' => $locacoes], JSON_UNESCAPED_UNICODE);
