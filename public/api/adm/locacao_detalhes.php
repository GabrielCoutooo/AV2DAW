
<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

$id_locacao = isset($_GET['id_locacao']) ? (int)$_GET['id_locacao'] : 0;

if ($id_locacao <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID da Locação inválido.']);
    exit;
}

$sql = "
    SELECT 
        L.id_locacao, L.data_hora_retirada, L.data_hora_prevista_devolucao, L.status, L.valor_total,
        C.nome AS cliente_nome, C.cpf AS cliente_cpf, C.email AS cliente_email,
        M.nome_modelo, M.marca, V.placa
    FROM LOCACAO L
    JOIN CLIENTE C ON L.id_cliente = C.id_cliente
    JOIN VEICULO V ON L.id_veiculo = V.id_veiculo
    JOIN MODELO M ON V.id_modelo = M.id_modelo
    WHERE L.id_locacao = ?
    LIMIT 1
";

$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id_locacao);
$stmt->execute();
$res = $stmt->get_result();
$dados = $res->fetch_assoc();
$stmt->close();
$con->close();

if (!$dados) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Locação não encontrada.']);
    exit;
}

// Separa os dados para retorno
$locacao = [
    'id_locacao' => $dados['id_locacao'],
    'data_hora_retirada' => $dados['data_hora_retirada'],
    'data_hora_prevista_devolucao' => $dados['data_hora_prevista_devolucao'],
    'status' => $dados['status'],
    'valor_total' => $dados['valor_total'],
];
$cliente = [
    'nome' => $dados['cliente_nome'],
    'cpf' => $dados['cliente_cpf'],
    'email' => $dados['cliente_email'],
];
$veiculo = [
    'nome_modelo' => $dados['nome_modelo'],
    'marca' => $dados['marca'],
    'placa' => $dados['placa'],
];

echo json_encode(['success' => true, 'locacao' => $locacao, 'cliente' => $cliente, 'veiculo' => $veiculo], JSON_UNESCAPED_UNICODE);
?>