<?php
require_once __DIR__ . '/../config/config.php';
require_once APP_PATH . '/config/connection.php';
require_once APP_PATH . '/config/auth-check.php';

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
if (empty($email) || empty($senha)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}
$stmt = $con->prepare("SELECT id_cliente, nome, email, senha_hash FROM CLIENTE WHERE email = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro interno. Tente novamente mais tarde.']);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email ou senha inválidos.']);
    exit;
}
$stmt->bind_result($id_cliente, $nome, $emailDB, $senhaHash);
$stmt->fetch();

if (!password_verify($senha, $senhaHash)) {
    echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos.']);
    exit;
}

// Login bem-sucedido
$_SESSION['usuario_id'] = $id_cliente;
$_SESSION['usuario_email'] = $emailDB;
$_SESSION['usuario_nome'] = !empty($nome) ? $nome : 'Usuário';

// Suporte ao "lembrar login" (cookie opcional)
if (!empty($_POST['lembrar'])) {
    setcookie('email_salvo', $email, time() + 60 * 60 * 24 * 30, "/"); // 30 dias
}

echo json_encode([
    'success' => true,
    'message' => 'Login realizado com sucesso!',
    'redirect' => BASE_URL . 'index.html'
]);
exit;
