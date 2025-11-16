<?php

// inclui conexão
$connectionPath = __DIR__ . '/../../config/connection.php';
if (file_exists($connectionPath)) require_once $connectionPath;
$id_locacao = isset($_GET['id_locacao']) ? intval($_GET['id_locacao']) : 0;
$loc = null;
if ($id_locacao && isset($con)) {
    $sql = "SELECT L.*, M.nome_modelo, M.marca FROM LOCACAO L INNER JOIN VEICULO V ON L.id_veiculo = V.id_veiculo INNER JOIN MODELO M ON V.id_modelo = M.id_modelo WHERE L.id_locacao = ? LIMIT 1";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i",$id_locacao);
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
<meta charset="utf-8"><title>Reserva Confirmada</title>
<link rel="stylesheet" href="/AV2DAW/public/css/style.css">
</head>
<body>
<main style="padding:30px">
<?php if ($loc): ?>
  <h1>Reserva Confirmada</h1>
  <p>Locação: #<?= htmlspecialchars($loc['id_locacao']) ?></p>
  <p>Veículo: <?= htmlspecialchars($loc['marca'].' '.$loc['nome_modelo']) ?></p>
  <p>Retirada: <?= htmlspecialchars($loc['data_hora_retirada']) ?></p>
  <p>Previsão devolução: <?= htmlspecialchars($loc['data_hora_prevista_devolucao']) ?></p>
  <p>Valor total: R$ <?= number_format($loc['valor_total'],2,',','.') ?></p>
  <a href="/AV2DAW/views/client/index.html">Voltar à Home</a>
<?php else: ?>
  <p>Reserva não encontrada.</p>
<?php endif; ?>
</main>
</body>
</html>