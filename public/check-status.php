<?php
require_once __DIR__ . '/../config/auth-check.php';

header('Content-Type: application/json');

$dadosUsuario = obterDadosUsuario();

if ($dadosUsuario) {
    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'nome' => $dadosUsuario['primeiro_nome']
        ]
    ]);
} else {
    echo json_encode(['loggedIn' => false]);
}
