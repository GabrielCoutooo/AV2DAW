<?php
session_start();

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: ../views/client/index.html');
    exit;
}
$tipoUsuario = $_SESSION['tipo_usuario'] ?? 'cliente';


$_SESSION = [];
session_destroy();

// Redireciona de acordo com o tipo
if ($tipoUsuario === 'admin') {
    header('Location: ../views/adm/login.html');
} else {
    header('Location: ../views/client/index.html');
}

exit;
