<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/config.php';

try {
    $queryPopulares = $pdo->query("
    SELECT 
    M.nome_modelo,
    M.categoria,
    M.preco_diaria_base,
    V.tipo_transmissao,
    V.capacidade_pessoas
    FROM VEICULO AS V
    JOIN MODELO AS M ON V.id_modelo = M.id_modelo
    WHERE V.disponivel = 1 AND M.categoria = 'Popular'
    LIMIT 4
    ");
    $populares = $queryPopulares->fetchAll(PDO::FETCH_ASSOC);

    $queryRecomendados = $pdo->query("
    SELECT 
    M.nome_modelo,
    M.categoria,
    M.preco_diaria_base,
    V.tipo_transmissao,
    V.capacidade_pessoas
    FROM VEICULO AS V
    JOIN MODELO AS M ON V.id_modelo = M.id_modelo
    WHERE V.disponivel = 1 AND M.categoria = 'Popular'
    LIMIT 4
    ");
    $recomendados = $queryRecomendados->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'populares' => $populares,
        'recomendados' => $recomendados
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar veículos: ' . $e->getMessage()]);
}
