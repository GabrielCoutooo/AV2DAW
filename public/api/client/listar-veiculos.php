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
    // Verifica se a nova estrutura de categorias existe
    $checkTable = $con->query("SHOW TABLES LIKE 'categoria'");
    $useNewStructure = $checkTable && $checkTable->num_rows > 0;

    if ($useNewStructure) {
        // Usar nova estrutura com múltiplas categorias
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
                V.disponivel,
                GROUP_CONCAT(C.nome ORDER BY C.nome SEPARATOR ', ') as categorias
            FROM modelo M
            INNER JOIN veiculo V ON V.id_modelo = M.id_modelo
            LEFT JOIN modelo_categoria MC ON M.id_modelo = MC.id_modelo
            LEFT JOIN categoria C ON MC.id_categoria = C.id_categoria
            GROUP BY M.id_modelo, V.id_veiculo
        ";
    } else {
        // Usar estrutura antiga (fallback)
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
                V.disponivel,
                M.categoria as categorias
            FROM modelo M
            INNER JOIN veiculo V ON V.id_modelo = M.id_modelo
        ";
    }

    $resultado = $con->query($sql);
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

// Monte o JSON que o script.js espera
$todos = buscarVeiculosPorCategoria($con);
$veiculosPorCategoria = [
    'populares'    => [],
    'recomendados' => [],
    'suv'          => [],
    'outros'       => [],
];

foreach ($todos as $carro) {
    // Pega as categorias do veículo (pode ser string separada por vírgula ou única categoria)
    $categoriasStr = isset($carro['categorias']) && $carro['categorias'] ? $carro['categorias'] : $carro['categoria'];
    $categorias = array_map('trim', explode(',', $categoriasStr));

    // Adiciona o carro em todas as categorias que ele pertence
    $adicionado = false;
    foreach ($categorias as $cat) {
        $catLower = strtolower($cat);
        if (strpos($catLower, 'popular') !== false) {
            $veiculosPorCategoria['populares'][] = $carro;
            $adicionado = true;
        }
        if (strpos($catLower, 'recomendado') !== false) {
            $veiculosPorCategoria['recomendados'][] = $carro;
            $adicionado = true;
        }
        if (strpos($catLower, 'suv') !== false) {
            $veiculosPorCategoria['suv'][] = $carro;
            $adicionado = true;
        }
    }

    // Se não foi adicionado em nenhuma categoria específica, adiciona em 'outros'
    if (!$adicionado) {
        $veiculosPorCategoria['outros'][] = $carro;
    }
}

echo json_encode($veiculosPorCategoria, JSON_UNESCAPED_UNICODE);
exit;
