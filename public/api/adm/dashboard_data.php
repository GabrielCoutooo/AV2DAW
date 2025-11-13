<?php
// Inclui arquivos essenciais (configuração e sessão)
require_once __DIR__ . '/../../config/config.php';

// Verifica a autenticação do administrador antes de retornar qualquer dado
session_start();
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode(['error' => 'Acesso não autorizado. Faça login como Administrador.']);
    exit();
}

// Define o cabeçalho para retornar JSON
header('Content-Type: application/json; charset=UTF-8');

// O código abaixo é a lógica de dados extraída do views/adm/index.php
// EM UM PROJETO REAL: Estas simulações devem ser substituídas por consultas ao banco de dados (tabelas VEICULO, MODELO, etc.)

$veiculos = [
    ['id' => 1, 'modelo' => 'Koenigsegg', 'marca' => 'Koenigsegg', 'imagem' => 'https://images.unsplash.com/photo-1628889045175-6e31ce1d7b35?w=300', 'categoria' => 'Esportivo', 'disponivel' => true],
    ['id' => 2, 'modelo' => 'Nissan GT-R', 'marca' => 'Nissan', 'imagem' => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=300', 'categoria' => 'Esportivo', 'disponivel' => true],
    ['id' => 3, 'modelo' => 'Rolls-Royce', 'marca' => 'Rolls-Royce', 'imagem' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=300', 'categoria' => 'Luxo', 'disponivel' => false],
    ['id' => 4, 'modelo' => 'All New Rush', 'marca' => 'Toyota', 'imagem' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=300', 'categoria' => 'SUV', 'disponivel' => true],
];

$vendedores = [
    ['nome' => 'CAUÁ', 'contato' => '(21) 96976-5432', 'turno' => '08:00 - 15:00', 'ultimo_modelo' => 'Nissan GT-R'],
    ['nome' => 'SOUZA', 'contato' => '(21) 96543-2109', 'turno' => '15:00 - 22:00', 'ultimo_modelo' => 'All New Rush'],
    ['nome' => 'ISAAS', 'contato' => '(21) 97654-3210', 'turno' => '15:00 - 22:00', 'ultimo_modelo' => 'Rolls-Royce'],
];

$checklists = [
    ['doc_cliente' => '485.780.362-19', 'modelo' => 'KOENIGSEGG', 'data' => '10/11/2024', 'tipo' => 'PÓS'],
    ['doc_cliente' => '803.214.135-62', 'modelo' => 'KOENIGSEGG', 'data' => '10/09/2024', 'tipo' => 'PRÉ'],
    ['doc_cliente' => '276.489.135-62', 'modelo' => 'ALL NEW RUSH', 'data' => '10/05/2024', 'tipo' => 'PÓS'],
];

$estatisticas = [
    'carros_alugados' => 36,
    'carros_disponiveis' => 24,
    'carros_manutencao' => 5,
    'vendas_mes' => 42
];

// Retorna todos os dados em um único JSON
echo json_encode([
    'veiculos' => $veiculos,
    'vendedores' => $vendedores,
    'checklists' => $checklists,
    'estatisticas' => $estatisticas
]);
?>