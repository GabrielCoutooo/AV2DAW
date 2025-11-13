<?php

/**
 * auth-check.php- Verifica se o usuário está logado
 * 
 * Responsável por gerenciar o estado de login do usuário e fornecer
 * funções auxiliares para verificação de autenticação em todo o sistema
 */
// Verificando se a sessão já não está ativa e iniciando-a para acesso as variáveis de autenticação
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/**
 * Verifica se o usuário está autenticado no sistema
 * 
 * @return bool Retorna true se o usuário estiver autenticado, caso contrário, false.
 */
function usuarioEstaLogado()
{
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}
/**
 * Obtém os dados do usuário logado de forma segura
 * 
 * @return array|null Retorna um array com os dados do usuário ou null se não estiver autenticado.
 */
function obterDadosUsuario()
{
    if (usuarioEstaLogado()) {
        $nomeCompleto = $_SESSION['usuario_nome'];
        if (!empty($nomeCompleto)) {
            // Extrai o primeiro nome para saudação personalizada
            $partes = explode(' ', trim($nomeCompleto));
            $primeiroNome = $partes[0];
            $primeiroSegundoNome = isset($partes[1]) ? $partes[0] . ' ' . $partes[1] : $partes[0];
        } else {
            // Se o nome estiver vazio usa fallback
            $primeiroNome = 'Visitante';
            $nomeCompleto = 'Visitante';
            $primeiroSegundoNome = 'Visitante';
        }
        return [
            'id' => $_SESSION['usuario_id'],
            'email' => $_SESSION['usuario_email'],
            'nome' => $nomeCompleto,
            // Extrai o primeiro nome para saudação personalizada
            'primeiro_nome' => $primeiroNome,
            // Extrai o primeiro e segundo nome para saudação personalizada
            'primeiro_segundo_nome' => $primeiroSegundoNome
        ];
    }
    return null;
}
