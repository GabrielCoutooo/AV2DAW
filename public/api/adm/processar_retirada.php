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

if ($id_locacao <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID da locação inválido.']);
    exit;
}

try {
    // Atualizar status da locação para 'Retirado'
    // O trigger vai automaticamente mudar o status_veiculo para 'Alugado'
    $sql = "UPDATE locacao SET status = 'Retirado' WHERE id_locacao = ? AND status = 'Reservado'";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id_locacao);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Check-out realizado com sucesso! Veículo marcado como Alugado.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Locação não encontrada ou já foi retirada.'
        ]);
    }

    $stmt->close();
    $con->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao processar retirada: ' . $e->getMessage()]);
}
