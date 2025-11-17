<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido.']);
    exit;
}

$id_locacao = isset($_POST['id_locacao']) ? (int)$_POST['id_locacao'] : 0;
$acao = $_POST['acao'] ?? 'devolver'; // 'devolver' (sem ocorrência) ou 'ocorrencia' (com vistoria)
$combustivel = $_POST['nivel_combustivel'] ?? 'Abaixo';
$km = $_POST['quilometragem_atual'] ?? 0;
$avarias = $_POST['avarias_registradas'] ?? null;
$itens_ok = $_POST['itens_ok'] ?? '';
$itens_nao_ok = $_POST['itens_nao_ok'] ?? '';

if ($id_locacao <= 0 || $km <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Dados de locação ou quilometragem inválidos.']);
    exit;
}

$con->begin_transaction();

try {
    $data_hora_devolucao = date('Y-m-d H:i:s');
    $status_final_locacao = 'Devolvido';

    // 1. REGISTRA A VISTORIA
    // Avaria só será registrada se a ação for 'ocorrencia' ou se houver itens não ok
    $avarias_detalhe = ($acao === 'ocorrencia' || !empty($itens_nao_ok)) ? ("Avarias/Obs: " . $avarias . " | Itens NÃO OK: " . $itens_nao_ok) : null;
    $acessorios_ok = empty($itens_nao_ok); // Se não houver itens não ok, acessórios estão confirmados

    $sqlVistoria = "INSERT INTO vistoria (id_locacao, tipo_vistoria, data_hora, nivel_combustivel, avarias_registradas, acessorios_confirmados)
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmtVistoria = $con->prepare($sqlVistoria);

    // Tipo 'Devolução' para Check-in
    $tipo_vistoria = 'Devolução';
    $stmtVistoria->bind_param('issssi', $id_locacao, $tipo_vistoria, $data_hora_devolucao, $combustivel, $avarias_detalhe, $acessorios_ok);
    $stmtVistoria->execute();
    $stmtVistoria->close();

    // 2. ATUALIZA A LOCAÇÃO (trigger vai atualizar status_veiculo automaticamente)
    $sqlLocacao = "UPDATE locacao SET status = ?, data_hora_real_devolucao = ? WHERE id_locacao = ?";
    $stmtLocacao = $con->prepare($sqlLocacao);
    $stmtLocacao->bind_param('ssi', $status_final_locacao, $data_hora_devolucao, $id_locacao);
    $stmtLocacao->execute();
    $stmtLocacao->close();

    // 3. ATUALIZA O VEÍCULO (Quilometragem - trigger já cuida do status_veiculo)
    $sqlVeiculo = "UPDATE veiculo v
                   JOIN locacao l ON v.id_veiculo = l.id_veiculo
                   SET v.quilometragem_atual = ?, v.disponivel = 1
                   WHERE l.id_locacao = ?";
    $stmtVeiculo = $con->prepare($sqlVeiculo);
    $stmtVeiculo->bind_param('ii', $km, $id_locacao);
    $stmtVeiculo->execute();
    $stmtVeiculo->close();

    $con->commit();
    $con->close();

    $msg = ($acao === 'ocorrencia' || !empty($itens_nao_ok)) ?
        "Check-in concluído. Vistoria registrada com OCORRÊNCIA." :
        "Check-in e devolução finalizados com sucesso.";

    echo json_encode(['success' => true, 'message' => $msg]);
} catch (Exception $e) {
    $con->rollback();
    $con->close();
    error_log("Erro no Check-in: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => "Erro no Check-in. Erro interno: " . $e->getMessage()]);
}
