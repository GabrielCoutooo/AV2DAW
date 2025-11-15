<?php

header('Content-Type: application/json');

$configPath = __DIR__ . '/../../../config/config.php';
$connectionPath = __DIR__ . '/../../../config/connection.php';
if(!file_exists($configPath)){
    echo json_encode([
        'error' => 'Arquivo config.php não encontrado.',
        'path' => $configPath
]);
exit;
}
require_once $configPath;
if(!file_exists($connectionPath)){
    echo json_encode([
        'error' => 'Arquivo connection.php não encontrado.',
        'path' => $connectionPath
]);
    exit;
}
require_once $connectionPath;
function buscarVeiculosPorCategoria($con, $categoria){
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
        WHERE M.categoria = ?
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $dados;
}
$categorias = ['Recomendado','Popular','SUV'];
$veiculosPorCategoria = [];
foreach ($categorias as $categoria) {
    $veiculosPorCategoria[$categoria] = buscarVeiculosPorCategoria($con, $categoria);
}
echo json_encode($veiculos, JSON_UNESCAPED_UNICODE);
exit;
