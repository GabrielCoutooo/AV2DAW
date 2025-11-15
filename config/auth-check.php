<?php

/**
 * auth-check.php- Verifica se o usuário está logado
 * 
 * Responsável por gerenciar o estado de login do usuário e fornecer
 * funções auxiliares para verificação de autenticação em todo o sistema
 */
// Inclui o arquivo de configuração principal, que já cuida de iniciar a sessão
require_once __DIR__ . '/config.php';
/**
 * Verifica se o usuário está autenticado no sistema
 * 
 * @return bool Retorna true se o usuário estiver autenticado, caso contrário, false.
 */
function clienteEstaLogado()
{
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function adminEstaLogado()
{
    return isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true;
}

function usuarioEstaLogado()
{
    return clienteEstaLogado() || adminEstaLogado();
}

function obterDadosUsuario()
{
    if (clienteEstaLogado()) {
        $nomeCompleto = $_SESSION['usuario_nome'] ?? '';
        $partes = explode(' ', trim($nomeCompleto));
        $primeiroNome = $partes[0] ?? '';
        $primeiroSegundoNome = isset($partes[1]) ? ($partes[0] . ' ' . $partes[1]) : $partes[0];
        return [
            'tipo' => 'cliente',
            'id' => $_SESSION['usuario_id'] ?? null,
            'email' => $_SESSION['usuario_email'] ?? null,
            'nome' => $nomeCompleto,
            'primeiro_nome' => $primeiroNome,
            'primeiro_segundo_nome' => $primeiroSegundoNome,
        ];
    }
    if (adminEstaLogado()) {
        return [
            'tipo' => 'admin',
            'id' => $_SESSION['admin_id'] ?? null,
            'email' => $_SESSION['admin_email'] ?? null,
            'nome' => $_SESSION['admin_nome'] ?? null,
            'primeiro_nome' => $_SESSION['admin_nome'],
            'primeiro_segundo_nome' => $_SESSION['admin_nome'],
        ];
    }
    return null;
}
