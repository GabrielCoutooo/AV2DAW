<?php

/**
 * Front Controller - Ponto de entrada da aplicação
 * Todas as requisições devem passar por este arquivo
 */

// Carregar configurações
require_once __DIR__ . '/../config/config.php';

// Definir o diretório raiz
define('APP_ROOT', dirname(__DIR__));
define('APP_URL', 'http://localhost/AV2DAW/public');

// Incluir Router ou fazer roteamento básico
$request = $_GET['page'] ?? 'home';

// Mapear requisições para controllers
$page = basename($request); // Prevenir directory traversal

try {
    switch ($page) {
        case 'home':
            require_once APP_ROOT . '/app/views/index.php';
            break;

        default:
            http_response_code(404);
            require_once APP_ROOT . '/app/views/404.php';
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "Erro: " . $e->getMessage();
}
