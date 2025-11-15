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
$tipo = trim($_POST['tipo'] ?? 'cliente'); // 'admin' ou 'cliente'

if (empty($email) || empty($senha)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}

/*
====================================================
1. LOGIN ADMIN — Email precisa existir e senha correta
====================================================
*/
if ($tipo === 'admin') {

    $stmt_admin = $con->prepare("SELECT id_admin, nome, email, senha_hash FROM ADMIN WHERE email = ?");
    $stmt_admin->bind_param("s", $email);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows !== 1) {
        echo json_encode(['success' => false, 'message' => 'Email de administrador não encontrado.']);
        exit;
    }
    $admin = $result_admin->fetch_assoc();

    // Se email existe mas a senha está ERRADA → bloqueia aqui mesmo
    if (!password_verify($senha, $admin['senha_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Senha incorreta para administrador.']);
        exit;
    }

    // --- INÍCIO: Garantia de sessão / cookie com path correto ---
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    // gera novo id de sessão por segurança
    session_regenerate_id(true);
    // força envio do cookie da sessão com path do projeto (ajuste se seu app estiver em outro subdiretório)
    setcookie(session_name(), session_id(), 0, '/php/AV2DAW');
    // --- FIM ---

    // Login ok
    $_SESSION['admin_id'] = $admin['id_admin'];
    $_SESSION['admin_nome'] = $admin['nome'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_logado'] = true;

    echo json_encode([
        'success' => true,
        'message' => 'Login de administrador realizado!',
        // caminho absoluto para evitar concatenações erradas
        'redirect' => '/php/AV2DAW/views/adm/index.php'
    ]);
    exit;
}

// ----------------------------------------------------
// 3. TENTA AUTENTICAR COMO CLIENTE (Se não for Admin ou se a senha do Admin estava errada)
// ----------------------------------------------------
$stmt_cliente = $con->prepare("SELECT id_cliente, nome, email, senha_hash FROM CLIENTE WHERE email = ?");
$stmt_cliente->bind_param("s", $email);
$stmt_cliente->execute();
$stmt_cliente->store_result();
if ($stmt_cliente->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Usuario não encontrado.']);
    exit;
}

$stmt_cliente->bind_result($id_cliente, $nome, $emailDB, $senhaHash);
$stmt_cliente->fetch();

if (!password_verify($senha, $senhaHash)) {
    echo json_encode(['success' => false, 'message' => 'Usuário ou senha incorretos.']);
    exit;
}

// --- INÍCIO: Garantia de sessão / cookie com path correto ---
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
session_regenerate_id(true);
setcookie(session_name(), session_id(), 0, '/php/AV2DAW');
// --- FIM ---

// Login de Cliente bem-sucedido
$_SESSION['usuario_id'] = $id_cliente;
$_SESSION['usuario_email'] = $emailDB;
$_SESSION['usuario_nome'] = $nome;
$_SESSION['is_admin'] = false;

// Suporte ao "lembrar login" (cookie opcional)
if (!empty($_POST['lembrar'])) {
    setcookie('email_salvo', $email, time() + 60 * 60 * 24 * 30, "/"); // 30 dias
}

echo json_encode([
    'success' => true,
    'message' => 'Login realizado com sucesso!',
    // caminho absoluto para cliente
    'redirect' => '/php/AV2DAW/views/client/index.html'
]);
exit;