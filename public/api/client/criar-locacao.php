<?php

header('Content-Type: application/json; charset=UTF-8');

// lê JSON do body
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Payload inválido']);
    exit;
}

$id_veiculo = isset($input['id_veiculo']) ? intval($input['id_veiculo']) : 0;
$dias = isset($input['dias']) ? intval($input['dias']) : 0;
$valor_total = isset($input['valor_total']) ? floatval($input['valor_total']) : 0.0;

session_start();
$id_cliente = isset($_SESSION['cliente_id']) ? intval($_SESSION['cliente_id']) : 1;

if ($id_veiculo <= 0 || $dias <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Dados obrigatórios ausentes']);
    exit;
}

// inclui conexão (ajusta caminho relativo)
$connectionPath = __DIR__ . '/../../../config/connection.php';
if (!file_exists($connectionPath)) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Conexão não encontrada']);
    exit;
}
require_once $connectionPath;

// prepara datas
$dt_retirada = date('Y-m-d H:i:s');
$dt_prev_devol = date('Y-m-d H:i:s', strtotime("+{$dias} days"));
$id_filial_retirada = 1;
$status = 'Reservado';

// Insere locação
$sql = "INSERT INTO LOCACAO (id_cliente,id_veiculo,id_filial_retirada,id_filial_devolucao,data_hora_retirada,data_hora_prevista_devolucao,status,valor_total)
        VALUES (?, ?, ?, NULL, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao preparar consulta: '.$con->error]);
    exit;
}

$bind = $stmt->bind_param("iiisssd", $id_cliente, $id_veiculo, $id_filial_retirada, $dt_retirada, $dt_prev_devol, $status, $valor_total);
$exec = $stmt->execute();
if (!$exec) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao inserir locação: '.$stmt->error]);
    exit;
}

$id_locacao = $con->insert_id;
$stmt->close();
$con->close();

echo json_encode(['success'=>true,'id_locacao'=>$id_locacao]);