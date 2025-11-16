<?php
header('Content-Type: application/json; charset=UTF-8');

$configPath     = __DIR__ . '/../../config/config.php';
$connectionPath = __DIR__ . '/../../config/connection.php';
$authPath       = __DIR__ . '/../../config/auth-check.php';

if (!file_exists($configPath) || !file_exists($connectionPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Arquivos de configuração ausentes.']);
    exit;
}

require_once $configPath;
require_once $connectionPath;
if (file_exists($authPath)) require_once $authPath;

$id_veiculo = isset($_GET['id_veiculo']) ? intval($_GET['id_veiculo']) : 0;

if ($id_veiculo <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID do veículo inválido ou não fornecido.']);
    exit;
}

$sql = "
    SELECT 
        V.id_veiculo,
        V.placa,
        V.ano,
        V.cor,
        V.tipo_transmissao,
        V.capacidade_pessoas,
        M.nome_modelo,
        M.marca,
        M.categoria,
        M.preco_diaria_base,
        M.imagem
    FROM VEICULO V
    INNER JOIN MODELO M ON V.id_modelo = M.id_modelo
    WHERE V.id_veiculo = ? 
    LIMIT 1
";

if (!isset($con) || !$con) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Conexão com banco inexistente.']);
    exit;
}

$stmt = $con->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno ao preparar a consulta: ' . $con->error]);
    exit;
}

$stmt->bind_param("i", $id_veiculo);
$stmt->execute();
$resultado = $stmt->get_result();
$veiculo = $resultado->fetch_assoc();
$stmt->close();
$con->close();

if ($veiculo) {
    echo json_encode(['success' => true, 'veiculo' => $veiculo], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Veículo não encontrado ou indisponível.']);
}