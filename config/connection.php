<?php
// Conexão com MYSQLi usando constantes do config
$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
//Verificando erros de conexão
if ($con->connect_error) {
    error_log("Erro de conexão: " . $con->connect_error);
    die("Desculpe, estamos enfrentando problemas técnicos. Por favor, tente novamente mais tarde.");
}

// Configurando o charset
$con->set_charset(DB_CHARSET);

// Função auxiliar para redirecionamento
function redirect($path)
{
    header("Location: " . BASE_URL . ltrim($path, '/'));
    exit();
}
