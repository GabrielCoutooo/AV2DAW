<?php
// Use the same session settings as the app
require_once __DIR__ . '/../config/config.php';

// Descobre o tipo do usuário antes de destruir a sessão
$tipoUsuario = $_SESSION['tipo_usuario'] ?? 'cliente';

// Limpa todos os dados da sessão atual
$_SESSION = [];

// Remove o cookie da sessão com os mesmos parâmetros utilizados na criação
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Destroi a sessão e o id
session_destroy();

// Redireciona de acordo com o tipo
if ($tipoUsuario === 'admin') {
    header('Location: ../views/adm/login.html');
} else {
    header('Location: ../views/client/index.html');
}

exit;
