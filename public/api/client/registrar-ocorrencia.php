
<?php
// Limpar qualquer buffer de saída antes de começar
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors', '0');
error_reporting(0);

try {
    require_once __DIR__ . '/../../../config/config.php';
    require_once __DIR__ . '/../../../config/connection.php';
    require_once __DIR__ . '/../../../config/auth-check.php';

    // Verificar se a conexão existe
    if (!isset($con) || !$con) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro de conexão com o banco de dados.']);
        exit;
    }

    if (!function_exists('clienteEstaLogado') || !clienteEstaLogado()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado.']);
        exit;
    }

    $id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Payload inválido.']);
        exit;
    }

    $id_locacao = isset($input['id_locacao']) ? intval($input['id_locacao']) : 0;
    $tipos = isset($input['tipos']) && is_array($input['tipos']) ? $input['tipos'] : [];
    $detalhes = isset($input['detalhes']) ? trim($input['detalhes']) : '';

    if ($id_cliente <= 0 || $id_locacao <= 0 || (empty($tipos) && $detalhes === '')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Dados ausentes ou inválidos.']);
        exit;
    }

    /* verifica posse da locação */
    $chk = $con->prepare("SELECT id_locacao FROM locacao WHERE id_locacao = ? AND id_cliente = ? LIMIT 1");
    if (!$chk) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $con->error]);
        exit;
    }
    $chk->bind_param('ii', $id_locacao, $id_cliente);
    $chk->execute();
    $res = $chk->get_result();
    if ($res->num_rows === 0) {
        $chk->close();
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Locação não pertence ao usuário.']);
        exit;
    }
    $chk->close();

    /* insere ocorrência */
    $tipos_json = json_encode(array_values($tipos), JSON_UNESCAPED_UNICODE);
    $data_registro = date('Y-m-d H:i:s');
    $status_inicial = 'Em Análise';

    $stmt = $con->prepare("INSERT INTO ocorrencia (id_locacao, tipos_selecionados, detalhes_adicionais, data_registro, status_ocorrencia) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao preparar inserção: ' . $con->error]);
        exit;
    }
    $stmt->bind_param('issss', $id_locacao, $tipos_json, $detalhes, $data_registro, $status_inicial);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao registrar ocorrência: ' . $stmt->error]);
        exit;
    }
    $id_ocorrencia = $con->insert_id;
    $stmt->close();

    /* opcional: atualizar status da locação */
    $upd = $con->prepare("UPDATE locacao SET status = ? WHERE id_locacao = ?");
    if ($upd) {
        $novoStatus = 'Em Análise';
        $upd->bind_param('si', $novoStatus, $id_locacao);
        $upd->execute();
        $upd->close();
    }

    $con->close();
    echo json_encode(['success' => true, 'message' => 'Ocorrência registrada com sucesso.', 'id_ocorrencia' => $id_ocorrencia]);
} catch (Exception $e) {
    // Log do erro para debug
    error_log("Erro em registrar-ocorrencia.php: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $e->getMessage()]);
} catch (Error $e) {
    // Captura erros fatais do PHP
    error_log("Erro fatal em registrar-ocorrencia.php: " . $e->getMessage() . " em " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro fatal: ' . $e->getMessage()]);
}
?>