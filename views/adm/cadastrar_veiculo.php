<?php
include_once '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $categoria = $_POST['categoria'];
    $imagem = $_POST['imagem'];
    
    try {
        // Primeiro cadastra o modelo
        $sqlModelo = "INSERT INTO MODELO (nome_modelo, marca, categoria, preco_diaria_base) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sqlModelo);
        $stmt->execute([$modelo, $marca, $categoria, 100.00]); // Preço base padrão
        
        $id_modelo = $pdo->lastInsertId();
        
        // Depois cadastra o veículo
        $sqlVeiculo = "INSERT INTO VEICULO (id_modelo, placa, ano, cor, tipo_transmissao, capacidade_pessoas, disponivel) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sqlVeiculo);
        $stmt->execute([$id_modelo, 'ABC1D23', 2024, 'Branco', 'Manual', 5, 1]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>