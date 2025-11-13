<?php
require_once __DIR__ . '/../config/config.php';
require_once APP_PATH . '/config/connection.php';
require_once APP_PATH . '/config/auth-check.php';

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

// ----------------------------------------------------
// 1. COLETA E VALIDAÇÃO INICIAL DOS DADOS
// ----------------------------------------------------
$email = trim($_POST['email'] ?? '');
$senha = trim($_POST['senha'] ?? '');

if (empty($email) || empty($senha)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}

// ----------------------------------------------------
// 2. TENTA AUTENTICAR COMO ADMINISTRADOR
// ----------------------------------------------------
$stmt_admin = $con->prepare("SELECT id_admin, nome, email, senha_hash FROM ADMIN WHERE email = ?");

if (!$stmt_admin) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno. Tente novamente mais tarde.']);
    exit;
}

$stmt_admin->bind_param("s", $email);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();
$is_admin_logged_in = false;

if ($result_admin->num_rows === 1) {
    $admin_row = $result_admin->fetch_assoc();

    // Verifica a senha do administrador
    if (password_verify($senha, $admin_row['senha_hash'])) {
        // Login de administrador bem-sucedido
        // session_start() já é chamada no config.php
        $_SESSION['admin_id'] = $admin_row['id_admin'];
        $_SESSION['admin_email'] = $admin_row['email'];
        $_SESSION['admin_nome'] = $admin_row['nome'];
        $_SESSION['admin_logado'] = true; // Flag essencial para proteção de rotas

        $is_admin_logged_in = true;
        
        echo json_encode([
            'success' => true,
            'message' => 'Login de Admin realizado com sucesso!',
            'redirect' => '../views/adm/index.php' // Redireciona para o dashboard admin
        ]);
        $stmt_admin->close();
        exit; // Finaliza o script após login bem-sucedido do Admin
    }
}
$stmt_admin->close();


// ----------------------------------------------------
// 3. TENTA AUTENTICAR COMO CLIENTE (Se não for Admin ou se a senha do Admin estava errada)
// ----------------------------------------------------
$stmt_cliente = $con->prepare("SELECT id_cliente, nome, email, senha_hash FROM CLIENTE WHERE email = ?");
if (!$stmt_cliente) {
    echo json_encode(['success' => false, 'message' => 'Erro interno. Tente novamente mais tarde.']);
    exit;
}
$stmt_cliente->bind_param("s", $email);
$stmt_cliente->execute();
$stmt_cliente->store_result();

if ($stmt_cliente->num_rows === 0) {
    // Se não encontrou nem Admin nem Cliente
    echo json_encode(['success' => false, 'message' => 'Email ou senha inválidos.']);
    exit;
}

$stmt_cliente->bind_result($id_cliente, $nome, $emailDB, $senhaHash);
$stmt_cliente->fetch();

if (!password_verify($senha, $senhaHash)) {
    echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos.']);
    exit;
}

// Login de Cliente bem-sucedido
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
    'redirect' => '../client/index.html'
]);
exit;
?>