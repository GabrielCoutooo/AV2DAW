<?php
// evita expor warnings no output e bufferiza qualquer output inesperado
ini_set('display_errors', '0');
error_reporting(E_ALL);
ob_start();

// garante que qualquer fatal/parse/notice será retornado como JSON
register_shutdown_function(function(){
    $err = error_get_last();
    if ($err !== null) {
        @ob_end_clean();
        http_response_code(500);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'error' => 'Fatal error: '.$err['message']]);
    }
});

// inclui config/config.php primeiro (sem espaços/BOM antes do <?php nesse arquivo)
$configPath = __DIR__ . '/../../../config/config.php';
if (!file_exists($configPath)) {
    @ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success'=>false,'error'=>'Configuração (config.php) não encontrada.']);
    exit;
}
require_once $configPath;

// inclui conexão (connection.php deve definir $con)
$connectionPath = __DIR__ . '/../../../config/connection.php';
if (!file_exists($connectionPath)) {
    @ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success'=>false,'error'=>'Conexão não encontrada']);
    exit;
}
require_once $connectionPath;

// limpa buffer de includes (avisos invisíveis)
@ob_end_clean();

// cabeçalho JSON
header('Content-Type: application/json; charset=UTF-8');

// valida $con
if (!isset($con) || !($con instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Conexão com BD não disponível (variável $con).']);
    exit;
}

// lê e valida payload
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Payload inválido ou JSON mal formado.']);
    exit;
}

$id_veiculo = isset($input['id_veiculo']) ? intval($input['id_veiculo']) : 0;
$dias = isset($input['dias']) ? intval($input['dias']) : 0;
$valor_total = isset($input['valor_total']) ? floatval($input['valor_total']) : 0.0;

// id do cliente a partir da sessão — ajuste conforme sua sessão (ver config.php)
$id_cliente = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
if ($id_cliente === null) {
    // se seu fluxo ainda usa um id fixo para testes, remova/ajuste aqui
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Usuário não autenticado.']);
    exit;
}

if ($id_veiculo <= 0 || $dias <= 0) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Dados obrigatórios ausentes.']);
    exit;
}

// prepara dados
$dt_retirada = date('Y-m-d H:i:s');
$dt_prev_devol = date('Y-m-d H:i:s', strtotime("+{$dias} days"));

// tentativa inicial: filial padrão (pode não existir)
$id_filial_retirada = 1;
$status = 'Reservado';

// ------------- Verifica existência da filial escolhida -------------
$checkStmt = $con->prepare("SELECT id_filial FROM filial WHERE id_filial = ? LIMIT 1");
if ($checkStmt) {
    $checkStmt->bind_param("i", $id_filial_retirada);
    $checkStmt->execute();
    $res = $checkStmt->get_result();
    if ($res->num_rows === 0) {
        // filial 1 não existe — tenta pegar a primeira filial disponível
        $row = $con->query("SELECT id_filial FROM filial LIMIT 1")->fetch_assoc();
        if ($row && isset($row['id_filial'])) {
            $id_filial_retirada = intval($row['id_filial']);
        } else {
            // nenhuma filial cadastrada — retorna erro claro ao frontend
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Nenhuma filial configurada. Cadastre ao menos uma filial antes de criar locações.']);
            exit;
        }
    }
    $checkStmt->close();
} else {
    // se a consulta de verificação falhar, aborta com mensagem
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao verificar filiais: '.$con->error]);
    exit;
}

// insere locação com tratamento de erros (mantém placeholders)
$sql = "INSERT INTO LOCACAO (id_cliente,id_veiculo,id_filial_retirada,id_filial_devolucao,data_hora_retirada,data_hora_prevista_devolucao,status,valor_total)
        VALUES (?, ?, ?, NULL, ?, ?, ?, ?)";
$stmt = $con->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao preparar consulta: '.$con->error]);
    exit;
}

$bindOk = $stmt->bind_param("iiisssd", $id_cliente, $id_veiculo, $id_filial_retirada, $dt_retirada, $dt_prev_devol, $status, $valor_total);
if (!$bindOk) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao bindar parâmetros: '.$stmt->error]);
    exit;
}

$exec = $stmt->execute();
if (!$exec) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Erro ao inserir locação: '.$stmt->error]);
    exit;
}

$id_locacao = $con->insert_id;
$stmt->close();
$con->close();

// resposta de sucesso
echo json_encode(['success'=>true,'id_locacao'=>$id_locacao]);
exit;

/*

=================AVISO SUPER IMPORTANTE =================
A logica final de finalização de pagamento para ir a checkout(quando o carro enfim é alugado)
é necessario a implementação de uma filial no banco de dados

copie esse codigo caso no dia da apresentação falhar:

INSERT INTO FILIAL (id_filial, nome_filial, endereco, telefone, email) 
VALUES (1, 'Matriz Rio de Janeiro', 'Rua Exemplo, 123 - Centro', '(21) 9999-0000', 'contato@alucar.com');

*/?>