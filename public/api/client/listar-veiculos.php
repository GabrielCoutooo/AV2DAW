<?php
header('Content-Type: application/json; charset=UTF-8');

$configPath     = __DIR__ . '/../../../config/config.php';
$connectionPath = __DIR__ . '/../../../config/connection.php';
$authPath       = __DIR__ . '/../../../config/auth-check.php';

if (!file_exists($configPath) || !file_exists($connectionPath) || !file_exists($authPath)) {
    echo json_encode(['error' => 'Arquivos de configuração ausentes.']);
    exit;
}

require_once $configPath;
require_once $connectionPath;
require_once $authPath;

// Se quiser exigir login, responda JSON em vez de redirecionar com HTML
if (!clienteEstaLogado()) {
    echo json_encode(['error' => 'Usuário não autenticado', 'loggedIn' => false]);
    exit;
}

function buscarVeiculosPorCategoria(mysqli $con): array
{
    $sql = "
        SELECT 
            M.id_modelo,
            M.nome_modelo,
            M.marca,
            M.categoria,
            M.preco_diaria_base,
            M.imagem,
            V.id_veiculo,
            V.placa,
            V.ano,
            V.cor,
            V.tipo_transmissao,
            V.capacidade_pessoas,
            V.quilometragem_atual,
            V.disponivel
        FROM MODELO M
        INNER JOIN VEICULO V ON V.id_modelo = M.id_modelo
    ";
    $resultado = $con->query($sql);
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

// Monte o JSON que o script.js espera
$todos = buscarVeiculosPorCategoria($con);
$veiculosPorCategoria = [
    'populares'    => [],
    'recomendados' => [],
    'suv'          => [],
    'outros'          => [],
];
foreach ($todos as $carro) {
    switch ($carro['categoria']) {
        case 'Popular':
            $veiculosPorCategoria['populares'][] = $carro;
            break;
        case 'Recomendado':
            $veiculosPorCategoria['recomendados'][] = $carro;
            break;
        case 'SUV':
            $veiculosPorCategoria['suv'][] = $carro;
            break;
        default:
            $veiculosPorCategoria['outros'][] = $carro;
            break;
    }
}

echo json_encode($veiculosPorCategoria, JSON_UNESCAPED_UNICODE);
exit;
