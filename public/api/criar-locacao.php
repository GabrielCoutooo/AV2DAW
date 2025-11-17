<?php

header('Content-Type: application/json; charset=UTF-8');
// evita saída acidental que quebra JSON
ob_start();

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Payload inválido']);
    exit;
}

$id_veiculo = isset($input['id_veiculo']) ? intval($input['id_veiculo']) : 0;
$dias = isset($input['dias']) ? intval($input['dias']) : 0;
$valor_total = isset($input['valor_total']) ? floatval($input['valor_total']) : 0.0;

// tenta identificar cliente pela sessão (fallback para 1)
if (session_status() === PHP_SESSION_NONE) session_start();
$id_cliente = isset($_SESSION['cliente_id']) ? intval($_SESSION['cliente_id']) : 1;

if ($id_veiculo <= 0 || $dias <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dados obrigatórios ausentes']);
    exit;
}

// localiza root do projeto de forma robusta (public/api -> ../../ = projeto)
$projectRoot = realpath(__DIR__ . '/../../');
$configPath = $projectRoot ? $projectRoot . '/config/config.php' : null;
$connectionPath = $projectRoot ? $projectRoot . '/config/connection.php' : null;
$authPath = $projectRoot ? $projectRoot . '/config/auth-check.php' : null;

if (!$configPath || !file_exists($configPath) || !$connectionPath || !file_exists($connectionPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Arquivos de configuração ausentes']);
    exit;
}

// inclui sem imprimir nada
require_once $configPath;
require_once $connectionPath;
if ($authPath && file_exists($authPath)) require_once $authPath;

// prepara datas
$dt_retirada = date('Y-m-d H:i:s');
$dt_prev_devol = date('Y-m-d H:i:s', strtotime("+{$dias} days"));

// id_filial_retirada padrão = 1
$id_filial_retirada = 1;
$status = 'Reservado';

// Insere locação (trigger vai atualizar status_veiculo automaticamente)
$sql = "INSERT INTO locacao (id_cliente,id_veiculo,id_filial_retirada,id_filial_devolucao,data_hora_retirada,data_hora_prevista_devolucao,status,valor_total)
        VALUES (?, ?, ?, NULL, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao preparar consulta: ' . $con->error]);
    exit;
}

// tipos: i(id_cliente) i(id_veiculo) i(id_filial_retirada) s(dt_retirada) s(dt_prev_devol) s(status) d(valor_total)
$bind = $stmt->bind_param("iiisssd", $id_cliente, $id_veiculo, $id_filial_retirada, $dt_retirada, $dt_prev_devol, $status, $valor_total);
if ($bind === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao bindar parâmetros: ' . $stmt->error]);
    exit;
}

$exec = $stmt->execute();
if (!$exec) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao inserir locação: ' . $stmt->error]);
    exit;
}

$id_locacao = $con->insert_id;
$stmt->close();
$con->close();

// limpa qualquer saída extra antes de enviar JSON
ob_end_clean();
echo json_encode(['success' => true, 'id_locacao' => $id_locacao]);
