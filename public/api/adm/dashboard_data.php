<?php
// Inclui arquivos essenciais (configuração e conexão)
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

// Inicia sessão caso não exista
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o administrador está logado
if (!adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso não autorizado. Faça login como Administrador.']);
    exit();
}

// Define o cabeçalho JSON
header('Content-Type: application/json; charset=UTF-8');

$idVeiculo = $_GET['id_veiculo'] ?? null;

// ================== VEÍCULOS ==================
$veiculos = [];
$sqlVeiculos = "
    SELECT v.id_veiculo, m.nome_modelo AS modelo, m.marca, v.ano, m.categoria, v.cor, v.placa, m.preco_diaria_base, m.imagem, v.disponivel
    FROM VEICULO v
    JOIN MODELO m ON v.id_modelo = m.id_modelo
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
$sqlAdmins = "SELECT id_admin, nome, email FROM ADMIN";
$result = $con->query($sqlAdmins);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vendedores[] = [
            'nome' => $row['nome'],
            'email' => $row['email'],
            'turno' => '08:00 - 18:00', // exemplo, pode adaptar se tiver tabela de turnos
        ];
    }
}

// ================== CHECKLISTS (LOCAÇÕES) ==================
$checklists = [];
$sqlLocacoes = "
    SELECT l.id_locacao, c.cpf AS doc_cliente, m.nome_modelo AS modelo, l.data_hora_retirada AS data, 
           CASE WHEN l.status='Reservado' THEN 'PRÉ' ELSE 'PÓS' END AS tipo
    FROM LOCACAO l
    JOIN CLIENTE c ON l.id_cliente = c.id_cliente
    JOIN VEICULO v ON l.id_veiculo = v.id_veiculo
    JOIN MODELO m ON v.id_modelo = m.id_modelo
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
$result = $con->query("SELECT COUNT(*) AS total FROM LOCACAO WHERE status='Retirado'");
if ($row = $result->fetch_assoc()) $estatisticas['carros_alugados'] = (int)$row['total'];

// Carros disponíveis
$result = $con->query("SELECT COUNT(*) AS total FROM VEICULO WHERE disponivel=1");
if ($row = $result->fetch_assoc()) $estatisticas['carros_disponiveis'] = (int)$row['total'];

// Carros em manutenção (disponivel=0)
$result = $con->query("SELECT COUNT(*) AS total FROM VEICULO WHERE disponivel=0");
if ($row = $result->fetch_assoc()) $estatisticas['carros_manutencao'] = (int)$row['total'];

// Vendas do mês (LOCACOES com data no mês atual)
$result = $con->query("
    SELECT COUNT(*) AS total 
    FROM LOCACAO 
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
