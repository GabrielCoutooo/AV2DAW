<?php
include_once '../../config/config.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $categoria = $_POST['categoria'];
    $preco = $_POST['preco'];

    $nomeArquivo = null;
    if (!empty($_FILES['imagem']['name'])) {
        $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
        $nomeArquivo = uniqid("carro") . '.' . $extensao;

        $destino = "../../public/uploads/carros/" . $nomeArquivo;
        move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
    }

    try {
        $sqlModelo = "INSERT INTO MODELO (nome_modelo, marca, categoria, preco_diaria_base, imagem) 
                      VALUES (?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sqlModelo);
        $stmt->bind_param("sssss", $modelo, $marca, $categoria, $preco, $nomeArquivo);
        $stmt->execute();

        $id_modelo = $con->insert_id;
        
        $sqlVeiculo = "INSERT INTO VEICULO (id_modelo, placa, ano, cor, tipo_transmissao, capacidade_pessoas, disponivel) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sqlVeiculo);
        $placa = 'ABC1D23';
        $ano = 2024; 
        $cor = 'Branco';
        $tipo_transmissao = 'Manual';
        $capacidade_pessoas = 5;
        $disponivel = 1;

        $stmt->bind_param("iisssii", $id_modelo, $placa, $ano, $cor, $tipo_transmissao, $capacidade_pessoas, $disponivel);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (mysqli_sql_exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
