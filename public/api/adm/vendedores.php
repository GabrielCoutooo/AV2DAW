<?php
// Output buffering para evitar HTML antes do JSON
ob_start();
set_exception_handler(function ($e) {
    http_response_code(500);
    error_log('VENDEDORES EXCEPTION: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $e->getMessage()]);
    exit();
});
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    error_log("VENDEDORES ERROR [$errno] $errstr em $errfile:$errline");
    echo json_encode(['success' => false, 'error' => "Erro interno: $errstr ($errfile:$errline)"]);
    exit();
});
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/../../../config/connection.php';
require_once __DIR__ . '/../../../config/auth-check.php';

if (!function_exists('adminEstaLogado') || !adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// CREATE
if ($metodo === 'POST') {
    $nome = trim($input['nome'] ?? '');
    $email = trim($input['email'] ?? '');
    $senha = trim($input['senha'] ?? '');
    $cpf = preg_replace('/\D+/', '', $input['cpf'] ?? '');
    $rg = trim($input['rg'] ?? '');
    $dataNasc = trim($input['data_nascimento'] ?? '');
    $genero = trim($input['genero'] ?? '');
    $telefone = trim($input['telefone'] ?? '');
    $endereco = trim($input['endereco'] ?? '');
    $dataAdmissao = trim($input['data_admissao'] ?? '');
    $turno = trim($input['turno'] ?? '');
    $carteira = trim($input['carteira_trabalho'] ?? '');
    $banco = trim($input['banco'] ?? '');
    $agencia = trim($input['agencia_conta'] ?? '');

    if ($nome === '' || $email === '' || $senha === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nome, email e senha são obrigatórios.']);
        exit;
    }

    // Verifica duplicação
    $chk = $con->prepare("SELECT id_admin FROM admin WHERE email = ? LIMIT 1");
    $chk->bind_param('s', $email);
    $chk->execute();
    if ($chk->get_result()->num_rows > 0) {
        $chk->close();
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Já existe um admin com este email.']);
        exit;
    }
    $chk->close();

    $hash = password_hash($senha, PASSWORD_BCRYPT);
    $stmt = $con->prepare("INSERT INTO admin (nome, email, senha_hash, cpf, rg, data_nascimento, genero, telefone, endereco, data_admissao, turno, carteira_trabalho, banco, agencia_conta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssssssssssss', $nome, $email, $hash, $cpf, $rg, $dataNasc, $genero, $telefone, $endereco, $dataAdmissao, $turno, $carteira, $banco, $agencia);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Falha ao criar admin: ' . $stmt->error]);
        $stmt->close();
        exit;
    }
    $id = $stmt->insert_id;
    $stmt->close();
    echo json_encode(['success' => true, 'id_admin' => $id]);
    exit;
}

// READ
if ($metodo === 'GET') {
    $id = isset($_GET['id_admin']) ? (int)$_GET['id_admin'] : 0;
    if ($id > 0) {
        $stmt = $con->prepare("SELECT id_admin, nome, email, cpf, rg, data_nascimento, genero, telefone, endereco, data_admissao, turno, carteira_trabalho, banco, agencia_conta FROM admin WHERE id_admin = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            echo json_encode(['success' => true, 'admin' => $row]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Admin não encontrado.']);
        }
    } else {
        $result = $con->query("SELECT id_admin, nome, email, cpf, telefone, turno FROM admin ORDER BY nome");
        $admins = [];
        while ($row = $result->fetch_assoc()) $admins[] = $row;
        echo json_encode(['success' => true, 'admins' => $admins]);
    }
    exit;
}

// UPDATE
if ($metodo === 'PUT') {
    $id = (int)($input['id_admin'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID inválido.']);
        exit;
    }
    $nome = trim($input['nome'] ?? '');
    $email = trim($input['email'] ?? '');
    $cpf = preg_replace('/\D+/', '', $input['cpf'] ?? '');
    $rg = trim($input['rg'] ?? '');
    $dataNasc = trim($input['data_nascimento'] ?? '');
    $genero = trim($input['genero'] ?? '');
    $telefone = trim($input['telefone'] ?? '');
    $endereco = trim($input['endereco'] ?? '');
    $dataAdmissao = trim($input['data_admissao'] ?? '');
    $turno = trim($input['turno'] ?? '');
    $carteira = trim($input['carteira_trabalho'] ?? '');
    $banco = trim($input['banco'] ?? '');
    $agencia = trim($input['agencia_conta'] ?? '');

    if ($nome === '' || $email === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Nome e email são obrigatórios.']);
        exit;
    }

    $stmt = $con->prepare("UPDATE admin SET nome=?, email=?, cpf=?, rg=?, data_nascimento=?, genero=?, telefone=?, endereco=?, data_admissao=?, turno=?, carteira_trabalho=?, banco=?, agencia_conta=? WHERE id_admin=?");
    $stmt->bind_param('sssssssssssssi', $nome, $email, $cpf, $rg, $dataNasc, $genero, $telefone, $endereco, $dataAdmissao, $turno, $carteira, $banco, $agencia, $id);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Falha ao atualizar: ' . $stmt->error]);
        $stmt->close();
        exit;
    }
    $stmt->close();
    echo json_encode(['success' => true]);
    exit;
}

// DELETE
if ($metodo === 'DELETE') {
    $id = (int)($input['id_admin'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID inválido.']);
        exit;
    }
    // Evita remover a si mesmo
    if ($id == ($_SESSION['admin_id'] ?? 0)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Você não pode excluir a si mesmo.']);
        exit;
    }
    $stmt = $con->prepare("DELETE FROM admin WHERE id_admin = ?");
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Falha ao excluir: ' . $stmt->error]);
        $stmt->close();
        exit;
    }
    $stmt->close();
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Método não suportado.']);
