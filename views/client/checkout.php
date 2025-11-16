<?php

// inclui conexão
$connectionPath = __DIR__ . '/../../config/connection.php';
if (file_exists($connectionPath)) require_once $connectionPath;

$id_locacao = isset($_GET['id_locacao']) ? intval($_GET['id_locacao']) : 0;
$loc = null;

if ($id_locacao && isset($con)) {
    $sql = "SELECT L.*, M.nome_modelo, M.marca FROM LOCACAO L INNER JOIN VEICULO V ON L.id_veiculo = V.id_veiculo INNER JOIN MODELO M ON V.id_modelo = M.id_modelo WHERE L.id_locacao = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id_locacao);
    $stmt->execute();
    $res = $stmt->get_result();
    $loc = $res->fetch_assoc();
    $stmt->close();
    $con->close();
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Confirmada - ALUCAR</title>
    <link rel="icon" href="/AV2DAW/public/images/logosemfundo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/AV2DAW/public/css/checkout_reserva.css">
</head>
<body>
    <main>
        <?php if ($loc): ?>
            <h1>Reserva Confirmada</h1>

            <div class="locacao-id">
                <p>Número da Locação</p>
                <div class="id-number">#<?= htmlspecialchars($loc['id_locacao']) ?></div>
            </div>

            <div class="info-container">
                
                <div class="veiculo-info">
                    <h3><i class="fas fa-car"></i> Veículo Alugado</h3>
                    <p><strong><?= htmlspecialchars($loc['marca'] . ' ' . $loc['nome_modelo']) ?></strong></p>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-calendar-alt"></i> Data de Retirada:</label>
                    <span><?= date('d/m/Y H:i', strtotime($loc['data_hora_retirada'])) ?></span>
                </div>

                <div class="info-row">
                    <label><i class="fas fa-calendar-check"></i> Previsão de Devolução:</label>
                    <span><?= date('d/m/Y H:i', strtotime($loc['data_hora_prevista_devolucao'])) ?></span>
                </div>

                <div class="valor-total">
                    <label>Valor Total da Locação</label>
                    <div class="amount">R$ <?= number_format($loc['valor_total'], 2, ',', '.') ?></div>
                </div>

            </div>

            <div class="button-container">
                <a href="/AV2DAW/views/client/index.html"><i class="fas fa-home"></i> Voltar à Home</a>
                <a href="/AV2DAW/views/client/venda.php"><i class="fas fa-car-side"></i> Alugar Outro Carro</a>
            </div>

        <?php else: ?>
            <div class="error-container">
                <p><i class="fas fa-exclamation-circle"></i> Reserva não encontrada.</p>
                <a href="/AV2DAW/views/client/index.html" style="display:inline-block; margin-top:15px;">Voltar à Home</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>