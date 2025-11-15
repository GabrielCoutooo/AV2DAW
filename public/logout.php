<?php
require_once __DIR__ . '/../config/config.php';

$_SESSION = [];
session_destroy();

// redireciona explicitamente para login admin ou home do cliente
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if (strpos($referer, '/AV2DAW/views/adm/') !== false || strpos($referer, '/php/AV2DAW/views/adm/') !== false) {
    header('Location: /php/AV2DAW/views/adm/login.html');
} else {
    header('Location: /php/AV2DAW/views/client/index.html');
}
exit;
