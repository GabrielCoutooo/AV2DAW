<?php
require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/config/auth-check.php';
require_once __DIR__ . '/../../config/connection.php';

header('Content-Type: application/json');

// 1. Verifica se o admin está logado
if (!adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acesso não autorizado.']);
    exit();
}

// 2. Pega o id_veiculo via POST ou JSON
$id_veiculo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id_veiculo'])) {
        $id_veiculo = intval($_POST['id_veiculo']);
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id_veiculo'])) {
            $id_veiculo = intval($data['id_veiculo']);
        }
    }
}

if (!$id_veiculo || $id_veiculo <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID do veículo não informado ou inválido']);
    exit();
}

// 3. Começa a transação para manter consistência
$con->begin_transaction();

try {
    // 3a. Remove revisões relacionadas ao veículo
    $stmt1 = $con->prepare("DELETE FROM REVISAO WHERE id_veiculo = ?");
    $stmt1->bind_param("i", $id_veiculo);
    if (!$stmt1->execute()) {
        throw new Exception("Falha ao remover revisões: " . $stmt1->error);
    }
    $stmt1->close();

    // 3b. Remove locações relacionadas ao veículo
    $stmt2 = $con->prepare("DELETE FROM LOCACAO WHERE id_veiculo = ?");
    $stmt2->bind_param("i", $id_veiculo);
    if (!$stmt2->execute()) {
        throw new Exception("Falha ao remover locações: " . $stmt2->error);
    }
    $stmt2->close();


    // 3c. Obter o id_modelo para deletar o modelo também
    $sqlGetModelo = "SELECT id_modelo FROM VEICULO WHERE id_veiculo = ?";
    $stmt = $con->prepare($sqlGetModelo);
    $stmt->bind_param("i", $id_veiculo);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    // 3d. Remove o veículo
    $stmt3 = $con->prepare("DELETE FROM VEICULO WHERE id_veiculo = ?");
    $stmt3->bind_param("i", $id_veiculo);
    if (!$stmt3->execute()) {
        throw new Exception("Falha ao remover veículo: " . $stmt3->error);
    }
    $stmt3->close();

    // 3e. Remove o modelo associado
    if ($row) {
        $id_modelo = $row['id_modelo'];
        $stmt4 = $con->prepare("DELETE FROM MODELO WHERE id = ?");
        $stmt4->bind_param("i", $id_modelo);
        if (!$stmt4->execute()) {
            throw new Exception("Falha ao remover modelo: " . $stmt4->error);
        }
        $stmt4->close();
    }

    // 4. Confirma a transação
    $con->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $con->rollback(); // desfaz alterações se houver erro
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
