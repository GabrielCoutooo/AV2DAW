<?php
// O caminho correto para o config.php a partir de /public
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/connection.php';
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}
$nome = trim($_POST['nome'] ?? '');
$sobrenome = trim($_POST['sobrenome'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$email = trim($_POST['email'] ?? '');
$confirmar_email = trim($_POST['confirmar_email'] ?? '');
$senha = trim($_POST['senha'] ?? '');
$confirmarSenha = trim($_POST['confirmar_senha'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$data_cadastro = date('Y-m-d H:i:s');

if (empty($nome) || empty($sobrenome) || empty($email) || empty($senha) || empty($confirmarSenha)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
    exit;
}
if ($email !== $confirmar_email || $senha !== $confirmarSenha) {
    echo json_encode(['success' => false, 'message' => 'Email ou senha não coincidem.']);
    exit;
}
$senhaHash = password_hash($senha, PASSWORD_BCRYPT);
$nomeCompleto = $nome . ' ' . $sobrenome;
$stmt = $con->prepare("INSERT INTO CLIENTE (nome, cpf, email, senha_hash,telefone,endereco,data_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $nomeCompleto, $cpf, $email, $senhaHash, $telefone, $endereco, $data_cadastro);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso!', 'redirect' => BASE_URL . 'login.html']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário. Tente novamente mais tarde.']);
}
$stmt->close();
$con->close();
/*teste*/
