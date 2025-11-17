<?php
require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/config/auth-check.php';
require_once __DIR__ . '/../../config/connection.php';

header("Content-Type: application/json; charset=UTF-8");

if (!adminEstaLogado()) {
    echo json_encode([
        "success" => false,
        "error" => "Acesso negado. Admin não está logado."
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = isset($_POST['acao']) ? $_POST['acao'] : 'criar';

    // ============ AÇÃO: CRIAR NOVO VEÍCULO ============
    if ($acao === 'criar') {
        // Dados do formulário

        $modelo = isset($_POST['modelo']) ? trim($_POST['modelo']) : '';
        $marca = isset($_POST['marca']) ? trim($_POST['marca']) : '';
        $ano = isset($_POST['ano']) ? (int)$_POST['ano'] : 0;
        $categoria = isset($_POST['categoria']) ? trim($_POST['categoria']) : '';
        $cor = isset($_POST['cor']) ? trim($_POST['cor']) : '';
        $placa = isset($_POST['placa']) ? strtoupper(trim($_POST['placa'])) : '';
        $preco = isset($_POST['preco_diaria_base']) ? trim($_POST['preco_diaria_base']) : '';
        $status = isset($_POST['disponivel']) && $_POST['disponivel'] == '1' ? 1 : 0;
        $status_veiculo = isset($_POST['status_veiculo']) ? trim($_POST['status_veiculo']) : 'Disponível';

        // Log temporário para depuração
        file_put_contents(__DIR__ . '/debug_placa.txt', date('Y-m-d H:i:s') . " | Placa recebida: '" . $placa . "'\n", FILE_APPEND);

        // Validação básica
        if ($placa === '' || $placa === '0') {
            echo json_encode(["success" => false, "message" => "Placa do veículo não pode ser vazia ou 0."]);
            exit;
        }

        // Upload da imagem
        $nomeArquivo = null;
        if (!empty($_FILES['imagem']['name'])) {
            $nomeArquivo = $_FILES['imagem']['name'];
            $destino = APP_PATH . "/public/images/uploads/carros/" . $nomeArquivo;
            move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
        }

        try {
            // 1) Inserir o modelo
            $sqlModelo = "INSERT INTO modelo (nome_modelo, marca, categoria, preco_diaria_base, imagem) 
                          VALUES (?, ?, ?, ?, ?)";

            $stmt = $con->prepare($sqlModelo);
            $stmt->bind_param("sssss", $modelo, $marca, $categoria, $preco, $nomeArquivo);
            $stmt->execute();

            $id_modelo = $con->insert_id;

            // 2) Inserir o veículo
            $sqlVeiculo = "INSERT INTO veiculo 
                           (id_modelo, placa, ano, cor, tipo_transmissao, capacidade_pessoas, disponivel, status_veiculo) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            // Valores fixos por enquanto
            $tipo_transmissao = "Manual";
            $capacidade_pessoas = 5;

            $stmt = $con->prepare($sqlVeiculo);
            // Tipos: id_modelo (i), placa (s), ano (i), cor (s), tipo_transmissao (s), capacidade_pessoas (i), disponivel (i), status_veiculo (s)
            $stmt->bind_param(
                "isissiis",
                $id_modelo,
                $placa,
                $ano,
                $cor,
                $tipo_transmissao,
                $capacidade_pessoas,
                $status,
                $status_veiculo
            );
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (mysqli_sql_exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    }

    // ============ AÇÃO: ATUALIZAR VEÍCULO ============
    else if ($acao === 'atualizar') {
        $id_veiculo = isset($_POST['id_veiculo']) ? (int)$_POST['id_veiculo'] : 0;

        if ($id_veiculo <= 0) {
            echo json_encode(["success" => false, "error" => "ID do veículo inválido: " . $_POST['id_veiculo']]);
            exit;
        }

        $modelo = isset($_POST['modelo']) ? $_POST['modelo'] : '';
        $marca = isset($_POST['marca']) ? $_POST['marca'] : '';
        $ano = isset($_POST['ano']) ? $_POST['ano'] : '';
        $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
        $cor = isset($_POST['cor']) ? $_POST['cor'] : '';
        $placa = isset($_POST['placa']) ? $_POST['placa'] : '';
        $preco = isset($_POST['preco_diaria_base']) ? $_POST['preco_diaria_base'] : '';
        $status = isset($_POST['disponivel']) && $_POST['disponivel'] == '1' ? 1 : 0;
        $status_veiculo = isset($_POST['status_veiculo']) ? trim($_POST['status_veiculo']) : 'Disponível';

        try {
            // Primeiro, obter o id_modelo do veículo
            $sqlGetModelo = "SELECT id_modelo FROM veiculo WHERE id_veiculo = ?";
            $stmt = $con->prepare($sqlGetModelo);
            if (!$stmt) {
                throw new Exception("Prepare falhou: " . $con->error);
            }
            $stmt->bind_param("i", $id_veiculo);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if (!$row) {
                echo json_encode(["success" => false, "error" => "Veículo não encontrado com ID: " . $id_veiculo]);
                exit;
            }

            $id_modelo = $row['id_modelo'];

            // Atualizar imagem se enviada
            $nomeArquivo = null;
            if (!empty($_FILES['imagem']['name'])) {
                $nomeArquivo = $_FILES['imagem']['name'];
                $destino = APP_PATH . "/public/images/uploads/carros/" . $nomeArquivo;
                move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
            }

            // 1) Atualizar o modelo (tabela modelo usa id_modelo)
            if ($nomeArquivo) {
                $sqlUpdateModelo = "UPDATE modelo SET nome_modelo = ?, marca = ?, categoria = ?, preco_diaria_base = ?, imagem = ? WHERE id_modelo = ?";
                $stmt = $con->prepare($sqlUpdateModelo);
                if (!$stmt) {
                    throw new Exception("Prepare falhou: " . $con->error);
                }
                $stmt->bind_param("sssssi", $modelo, $marca, $categoria, $preco, $nomeArquivo, $id_modelo);
            } else {
                $sqlUpdateModelo = "UPDATE modelo SET nome_modelo = ?, marca = ?, categoria = ?, preco_diaria_base = ? WHERE id_modelo = ?";
                $stmt = $con->prepare($sqlUpdateModelo);
                if (!$stmt) {
                    throw new Exception("Prepare falhou: " . $con->error);
                }
                $stmt->bind_param("ssssi", $modelo, $marca, $categoria, $preco, $id_modelo);
            }
            $stmt->execute();
            $stmt->close();

            // 2) Atualizar o veículo (tabela veiculo usa id_veiculo)
            $sqlUpdateVeiculo = "UPDATE veiculo SET placa = ?, ano = ?, cor = ?, disponivel = ?, status_veiculo = ? WHERE id_veiculo = ?";
            $stmt = $con->prepare($sqlUpdateVeiculo);
            if (!$stmt) {
                throw new Exception("Prepare falhou: " . $con->error);
            }
            // Tipos: placa (s), ano (i), cor (s), disponivel (i), status_veiculo (s), id_veiculo (i)
            $stmt->bind_param("sisisi", $placa, $ano, $cor, $status, $status_veiculo, $id_veiculo);
            $stmt->execute();
            $stmt->close();

            echo json_encode(["success" => true, "message" => "Veículo atualizado com sucesso"]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    }

    // ============ AÇÃO: DELETAR VEÍCULO ============
    else if ($acao === 'deletar') {
        $id_veiculo = isset($_POST['id_veiculo']) ? (int)$_POST['id_veiculo'] : 0;

        if ($id_veiculo <= 0) {
            echo json_encode(["success" => false, "message" => "ID do veículo inválido"]);
            exit;
        }

        try {
            // Obter id_modelo para deletar também
            $sqlGetModelo = "SELECT id_modelo FROM veiculo WHERE id_veiculo = ?";
            $stmt = $con->prepare($sqlGetModelo);
            $stmt->bind_param("i", $id_veiculo);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if (!$row) {
                echo json_encode(["success" => false, "message" => "Veículo não encontrado"]);
                exit;
            }

            $id_modelo = $row['id_modelo'];

            // Deletar o veículo (tabela veiculo usa id_veiculo)
            $sqlDeleteVeiculo = "DELETE FROM veiculo WHERE id_veiculo = ?";
            $stmt = $con->prepare($sqlDeleteVeiculo);
            $stmt->bind_param("i", $id_veiculo);
            $stmt->execute();

            // Deletar o modelo (tabela modelo usa id_modelo)
            $sqlDeleteModelo = "DELETE FROM modelo WHERE id_modelo = ?";
            $stmt = $con->prepare($sqlDeleteModelo);
            $stmt->bind_param("i", $id_modelo);
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (mysqli_sql_exception $e) {
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Ação não reconhecida"]);
    }
}
