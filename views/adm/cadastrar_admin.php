<?php
require_once __DIR__ . '/../../config/config.php';
require_once APP_PATH . '/config/auth-check.php';
require_once __DIR__ . '/../../config/connection.php';

header("Content-Type: application/json; charset=UTF-8");

if (!adminEstaLogado()) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Acesso negado. Apenas administradores logados podem cadastrar novos admins."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['acao'] ?? '') !== 'cadastrar') {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Requisição inválida."]);
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($nome) || empty($email) || empty($senha)) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Nome, E-mail e Senha são obrigatórios."]);
    exit;
}

$senhaHash = password_hash($senha, PASSWORD_BCRYPT);

try {
    // Insere apenas as colunas que existem na tabela ADMIN
    $sql = "INSERT INTO ADMIN (nome, email, senha_hash) VALUES (?, ?, ?)";
            
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $con->error);
    }
    
    $stmt->bind_param("sss", $nome, $email, $senhaHash);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Administrador cadastrado com sucesso!"]);
    } else {
        if ($con->errno === 1062) {
            throw new Exception("E-mail já cadastrado no sistema.");
        }
        throw new Exception("Erro ao cadastrar: " . $stmt->error);
    }
    
    $stmt->close();
    $con->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>