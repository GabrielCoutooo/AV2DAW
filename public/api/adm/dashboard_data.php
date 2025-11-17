<?php
// Tratamento global de erro para garantir resposta JSON
set_exception_handler(function ($e) {
    http_response_code(500);
    error_log('DASHBOARD EXCEPTION: ' . $e->getMessage());
    echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
    exit();
});
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    error_log("DASHBOARD ERROR [$errno] $errstr em $errfile:$errline");
    echo json_encode(['error' => "Erro interno: $errstr ($errfile:$errline)"]);
    exit();
});

// Suprime avisos de exibição para o usuário
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Inclui arquivos essenciais (configuração e conexão)
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

// Define o cabeçalho JSON
header('Content-Type: application/json; charset=UTF-8');

// Verifica se o administrador está logado
if (!adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso não autorizado. Faça login como Administrador.']);
    exit();
}

$idVeiculo = $_GET['id_veiculo'] ?? null;

// ================== VEÍCULOS ==================
$veiculos = [];
$sqlVeiculos = "
    SELECT v.id_veiculo, m.nome_modelo AS modelo, m.marca, v.ano, m.categoria, v.cor, v.placa, m.preco_diaria_base, m.imagem, v.disponivel, v.status_veiculo
    FROM veiculo v
    JOIN modelo m ON v.id_modelo = m.id_modelo
";
$result = $con->query($sqlVeiculos);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ajusta caminho da imagem caso não seja URL
        if (!filter_var($row['imagem'], FILTER_VALIDATE_URL)) {
            $row['imagem'] = "/AV2DAW/public/images/uploads/carros/" . $row['imagem'];
        }
        $veiculos[] = $row;
    }
}

// ================== VENDEDORES (ADMINS) ==================
$vendedores = [];
$sqlAdmins = "SELECT id_admin, nome, email FROM admin";
$result = $con->query($sqlAdmins);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vendedores[] = [
            'id_admin' => $row['id_admin'],
            'nome' => $row['nome'],
            'email' => $row['email'],
            'turno' => '08:00 - 18:00', // valor padrão
        ];
    }
} else {
    error_log("Erro SQL vendedores: " . $con->error);
}

// ================== CHECKLISTS (LOCAÇÕES) ==================
$checklists = [];
$sqlLocacoes = "
    SELECT l.id_locacao, c.cpf AS doc_cliente, m.nome_modelo AS modelo, l.data_hora_retirada AS data, 
           CASE WHEN l.status='Reservado' THEN 'PRÉ' ELSE 'PÓS' END AS tipo
    FROM locacao l
    JOIN cliente c ON l.id_cliente = c.id_cliente
    JOIN veiculo v ON l.id_veiculo = v.id_veiculo
    JOIN modelo m ON v.id_modelo = m.id_modelo
    ORDER BY l.data_hora_retirada DESC
    LIMIT 10
";

if ($result = $con->query($sqlLocacoes)) {
    while ($row = $result->fetch_assoc()) {
        $checklists[] = [
            'id_locacao' => $row['id_locacao'],
            'doc_cliente' => $row['doc_cliente'],
            'modelo' => $row['modelo'],
            'data' => date('d/m/Y', strtotime($row['data'])),
            'tipo' => $row['tipo']
        ];
    }
}

// ================== ESTATÍSTICAS ==================
$estatisticas = [
    'carros_alugados' => 0,
    'carros_disponiveis' => 0,
    'carros_manutencao' => 0,
    'vendas_mes' => 0
];

// Carros alugados (status = Retirado)
$result = $con->query("SELECT COUNT(*) AS total FROM locacao WHERE status='Retirado'");
if ($row = $result->fetch_assoc()) $estatisticas['carros_alugados'] = (int)$row['total'];

// Carros disponíveis (status_veiculo = 'Disponível' OU disponivel=1 se status não existir)
$result = $con->query("SELECT COUNT(*) AS total FROM veiculo WHERE status_veiculo='Disponível' OR (status_veiculo IS NULL AND disponivel=1)");
if ($row = $result->fetch_assoc()) $estatisticas['carros_disponiveis'] = (int)$row['total'];

// Carros em manutenção (status_veiculo='Manutenção')
$result = $con->query("SELECT COUNT(*) AS total FROM veiculo WHERE status_veiculo='Manutenção'");
if ($row = $result->fetch_assoc()) $estatisticas['carros_manutencao'] = (int)$row['total'];

// Vendas do mês (LOCACOES com data no mês atual)
$result = $con->query("
    SELECT COUNT(*) AS total 
    FROM locacao 
    WHERE MONTH(data_hora_retirada) = MONTH(CURDATE()) 
      AND YEAR(data_hora_retirada) = YEAR(CURDATE())
");
if ($row = $result->fetch_assoc()) $estatisticas['vendas_mes'] = (int)$row['total'];

// ================== RETORNA JSON ==================
echo json_encode([
    'veiculos' => $veiculos,
    'vendedores' => $vendedores,
    'checklists' => $checklists,
    'estatisticas' => $estatisticas
]);

// Envia o buffer e encerra
ob_end_flush();
exit;
