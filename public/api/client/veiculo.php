<?php
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!clienteEstaLogado()) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado', 'loggedIn' => false]);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de veículo inválido']);
    exit;
}

$sql = "
    SELECT 
        M.id_modelo,
        M.nome_modelo,
        M.marca,
        M.categoria,
        M.preco_diaria_base,
        M.imagem,
        V.id_veiculo,
        V.placa,
        V.ano,
        V.cor,
        V.tipo_transmissao,
        V.capacidade_pessoas,
        V.quilometragem_atual,
        V.disponivel
    FROM MODELO M
    INNER JOIN VEICULO V ON V.id_modelo = M.id_modelo
    WHERE V.id_veiculo = ?
";

$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$veiculo = $res->fetch_assoc();
$stmt->close();

if (!$veiculo) {
    http_response_code(404);
    echo json_encode(['error' => 'Veículo não encontrado']);
    exit;
}

echo json_encode(['veiculo' => $veiculo], JSON_UNESCAPED_UNICODE);
exit;
