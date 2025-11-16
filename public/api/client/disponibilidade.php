<?php
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!clienteEstaLogado()) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuário não autenticado']);
    exit;
}

// Parâmetros
$tipo = isset($_GET['tipo']) && in_array($_GET['tipo'], ['retirada', 'devolucao']) ? $_GET['tipo'] : 'retirada';
$data = $_GET['data'] ?? '';
$hora = $_GET['hora'] ?? '';
$modeloId = isset($_GET['modelo_id']) ? (int)$_GET['modelo_id'] : 0;
$pesquisa = trim($_GET['q'] ?? '');

if ($data && $hora) {
    // Monta datetime
    $dt = date('Y-m-d H:i:00', strtotime($data . ' ' . $hora));
} else {
    $dt = date('Y-m-d H:i:00'); // fallback: agora
}

// Base query
$sql = "SELECT M.id_modelo, M.nome_modelo, M.marca, M.categoria, M.preco_diaria_base, M.imagem,
               V.id_veiculo, V.placa, V.ano, V.cor, V.tipo_transmissao, V.capacidade_pessoas, V.disponivel
        FROM MODELO M
        INNER JOIN VEICULO V ON V.id_modelo = M.id_modelo
        WHERE V.disponivel = 1";

$params = [];
$types = '';

if ($modeloId > 0) {
    $sql .= " AND M.id_modelo = ?";
    $params[] = $modeloId;
    $types .= 'i';
}

if ($pesquisa !== '') {
    $sql .= " AND (M.nome_modelo LIKE ? OR M.categoria LIKE ?)";
    $like = '%' . $pesquisa . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

// Exclui veículos com locações ativas sobrepostas ao momento consultado
$sql .= " AND NOT EXISTS (SELECT 1 FROM LOCACAO L
                          WHERE L.id_veiculo = V.id_veiculo
                            AND L.status IN ('Reservado','Retirado')
                            AND ? BETWEEN L.data_hora_retirada AND L.data_hora_prevista_devolucao)";
$params[] = $dt;
$types .= 's';

$stmt = $con->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$lista = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Retorna JSON
echo json_encode([
    'momento_consulta' => $dt,
    'total' => count($lista),
    'veiculos' => $lista
], JSON_UNESCAPED_UNICODE);
