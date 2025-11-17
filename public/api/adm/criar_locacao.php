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
$idVeiculo = isset($input['id_veiculo']) ? (int)$input['id_veiculo'] : 0;
$cpf       = isset($input['cpf']) ? preg_replace('/\D+/', '', $input['cpf']) : '';
$dias      = isset($input['dias']) ? (int)$input['dias'] : 0;
$temSeguro = isset($input['tem_seguro']) ? (bool)$input['tem_seguro'] : false;

if ($idVeiculo <= 0 || $dias <= 0 || $cpf === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos. Informe id_veiculo, cpf e dias.']);
    exit;
}

// Localiza cliente pelo CPF (normaliza ambos os lados para match)
$stmt = $con->prepare("SELECT id_cliente FROM cliente WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ? LIMIT 1");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao preparar consulta de cliente: ' . $con->error]);
    exit;
}
$stmt->bind_param('s', $cpf);
$stmt->execute();
$res = $stmt->get_result();
$cli = $res->fetch_assoc();
$stmt->close();

if (!$cli) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Cliente não encontrado para o CPF informado.']);
    exit;
}
$idCliente = (int)$cli['id_cliente'];

// Verifica status do veículo e obtém preço diária
$st = $con->prepare("SELECT v.status_veiculo, m.preco_diaria_base FROM veiculo v JOIN modelo m ON v.id_modelo = m.id_modelo WHERE v.id_veiculo=? LIMIT 1");
$st->bind_param('i', $idVeiculo);
$st->execute();
$vr = $st->get_result()->fetch_assoc();
$st->close();
if (!$vr) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Veículo não encontrado.']);
    exit;
}
if (isset($vr['status_veiculo'])) {
    $sv = $vr['status_veiculo'];
    if ($sv === 'Alugado' || $sv === 'Manutenção') {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Veículo indisponível no momento.']);
        exit;
    }
}
$precoDiaria = (float)$vr['preco_diaria_base'];
$subtotal = $precoDiaria * $dias;
$seguro = $temSeguro ? round($subtotal * 0.091, 2) : 0;
$taxaLocadora = 50.00;
$valorTotal = $subtotal + $seguro + $taxaLocadora;

// Cria locação como Reservado; triggers cuidarão do status do veículo quando houver retirada
// Usa data_hora_prevista_devolucao (schema correto) e id_filial_retirada = 1 (padrão)
$stmt = $con->prepare("INSERT INTO locacao (id_cliente, id_veiculo, id_filial_retirada, data_hora_retirada, data_hora_prevista_devolucao, status, valor_total) VALUES (?, ?, 1, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), 'Reservado', ?)");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao preparar inserção: ' . $con->error]);
    exit;
}
$stmt->bind_param('iiid', $idCliente, $idVeiculo, $dias, $valorTotal);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Falha ao criar locação: ' . $stmt->error]);
    $stmt->close();
    exit;
}
$idLocacao = $stmt->insert_id;
$stmt->close();

// Retorna dados
echo json_encode([
    'success' => true,
    'id_locacao' => $idLocacao,
    'status' => 'Reservado',
    'valor_total' => $valorTotal,
    'detalhes' => [
        'subtotal' => $subtotal,
        'seguro' => $seguro,
        'taxa_locadora' => $taxaLocadora,
        'tem_seguro' => $temSeguro
    ]
]);
