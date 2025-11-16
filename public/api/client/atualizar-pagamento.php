<?php

header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors','0'); error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!function_exists('clienteEstaLogado') || !clienteEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Usuário não autenticado.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$metodo = $input['metodo'] ?? '';
$cartao = preg_replace('/\D+/', '', ($input['cartao'] ?? ''));

$id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;
if ($id_cliente <= 0) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'ID do cliente inválido.']); exit; }

$cartao_last4 = $cartao ? substr($cartao, -4) : null;

// tenta atualizar — pressupõe colunas metodo_pagamento e cartao_ultimo4 na tabela CLIENTE
$sql = "UPDATE CLIENTE SET metodo_pagamento = ?, cartao_ultimo4 = ? WHERE id_cliente = ?";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao preparar atualização: '.$con->error]);
    exit;
}
$stmt->bind_param('ssi', $metodo, $cartao_last4, $id_cliente);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao salvar preferências: '.$stmt->error]);
    exit;
}
$stmt->close();
$con->close();

echo json_encode(['success'=>true,'message'=>'Preferências de pagamento salvas.']);