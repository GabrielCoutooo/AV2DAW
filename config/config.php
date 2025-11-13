<?php

// CONFIGURAÇÕES GERAIS
define('NOME_SITE', 'alucar');
define('EMAIL_CONTATO', 'gabrielccsilva@gmail.com');
define('TELEFONE_CONTATO', '(21) 98833-9569');
// CONFIGURAÇÕES DO BANCO DE DADOS
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'alucar');
define('DB_CHARSET', 'utf8mb4');
/** 
 * Configuração de diretório
 */
define('APP_PATH', dirname(__DIR__));
define('INCLUDES_PATH', APP_PATH . '/includes');
define('BASE_URL', 'http://localhost/AV2DAW/public/');
/**
 * CONFIGURAÇÕES DE SESSÃO SEGURA
 * 
 * Este bloco configura os parâmetros de sessão do PHP para garantir
 * autenticação segura e proteção contra ataques comuns.
 */
define('SESSION_NAME', 'alucar_AUTH');

//Tempo de vida da sessão em segundos (86400 = 24 horas)
define('SESSION_LIFETIME', 86400);

session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => '',

    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_name(SESSION_NAME);
session_start();
