<?php
// Inclui arquivos de configuração e autenticação
$projectRoot = __DIR__ . '/../../'; 
$configPath = realpath($projectRoot . 'config/config.php');
$authCheckPath = realpath($projectRoot . 'config/auth-check.php');

if ($configPath && file_exists($configPath)) require_once $configPath;
if ($authCheckPath && file_exists($authCheckPath)) require_once $authCheckPath;

// Redireciona se o cliente não estiver logado
if (!clienteEstaLogado()) {
    header('Location: login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento - ALUCAR</title>
    <link rel="icon" href="../../public/images/logosemfundo.png" type="image/png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos base do protótipo - Fundo Azul */
        .container-pagamento {
            background-color: #00bfff;
            padding: 40px 20px;
            min-height: 90vh;
            display: flex;
            justify-content: center;
        }

        .layout-pagamento {
            display: grid;
            grid-template-columns: 2fr 1.5fr;
            gap: 30px;
            max-width: 1200px;
            width: 100%;
        }

        .card {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Card Principal do Carro */
        .card-carro-detalhes {
            display: flex;
            gap: 20px;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .card-carro-detalhes img {
            width: 180px;
            height: 120px;
            object-fit: contain;
            border-radius: 8px;
        }

        .carro-texto h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .carro-texto p {
            color: #555;
            margin-bottom: 10px;
        }
        
        .carro-specs {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            color: #777;
        }
        .carro-specs i { color: #00bfff; }
        .carro-protecao { margin-top: 10px; font-size: 0.95rem; color: #27ae60; }

        /* Card do Itinerário (Inputs) */
        .card-locais {
            margin-top: 20px;
        }

        .card-locais h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .local-info {
            border-left: 3px solid #00bfff;
            padding-left: 15px;
            margin-bottom: 20px;
            padding: 15px 0 15px 15px;
        }

        .local-info p strong { 
            color: #333;
            display: block;
            margin-bottom: 8px;
        }

        .entrada-formulario {
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .entrada-formulario:focus {
            outline: none;
            border-color: #00bfff;
            box-shadow: 0 0 5px rgba(0, 191, 255, 0.3);
        }

        .servico-shuttle { 
            font-size: 0.9rem; 
            color: #555; 
            margin-top: 8px;
        }

        .local-info:last-of-type {
            border-left-color: #cc0000;
        }

        .btn-alterar {
            background-color: #27ae60;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-alterar:hover {
            background-color: #229954;
        }

        /* Coluna Direita: Resumo */
        .resumo-reserva {
            padding: 30px;
            border-radius: 15px;
            background-color: #fff;
        }

        .reserva-header {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 20px;
            text-align: left; 
            display: flex; 
            justify-content: space-between;
            align-items: center;
        }

        .dias-badge {
            background-color: #cc0000;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .linha-valor {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            font-size: 1rem;
            color: #555;
        }

        .linha-valor:last-of-type {
            border-bottom: none;
            margin-top: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .linha-valor strong {
            color: #cc0000;
        }

        .btn-pagar {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            background-color: #00bfff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-pagar:hover {
            background-color: #0099cc;
        }

        .btn-pagar:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        /* Estilos do Modal (escolha de pagamento) */
        .modal-pagamento-container {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            display: none; 
            justify-content: center;
            align-items: center;
        }

        .modal-content-pagamento {
             background-color: #fefefe;
             padding: 30px;
             border-radius: 10px;
             width: 90%;
             max-width: 450px;
             position: relative;
        }

        .opcoes-pagamento button {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .opcoes-pagamento button:hover {
            border-color: #00bfff;
            background-color: #f0f8ff;
        }

        .opcoes-pagamento button.selected {
            border-color: #00bfff;
            background-color: #00bfff;
            color: white;
        }

        .btn-confirmar {
            width: 100%;
            padding: 15px;
            margin-top: 15px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-confirmar:hover {
            background-color: #229954;
        }

        .btn-confirmar:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .fechar {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .fechar:hover {
            color: #000;
        }

        @media (max-width: 900px) {
            .layout-pagamento {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <img src="../../public/images/logo.png" alt="ALUCAR Logo" class="logo-img">
            </div>
            <nav class="nav-links">
                <a href="index.html" class="nav-link">Início</a>
                <a href="#servicos" class="nav-link">Serviços</a>
                <a href="contato.html" class="nav-link">Contato</a>
            </nav>
            <div class="login-btn" id="user-auth-container"></div>
        </div>
    </header>

    <div class="container-pagamento">
        <div class="layout-pagamento">
            
            <div class="coluna-esquerda">
                <p id="feedback-erro-resumo" style="color:white; font-weight:bold; margin-bottom:15px;"></p>
                
                <div class="card">
                    <div class="card-carro-detalhes">
                        <img id="carro-img-resumo" src="" alt="Veículo para aluguel">
                        <div class="carro-texto">
                            <h3 id="carro-modelo-resumo">Carregando...</h3>
                            <p id="carro-categoria-resumo"></p>
                            <div class="carro-specs">
                                <span><i class="fas fa-gas-pump"></i> 90L</span>
                                <span><i class="fas fa-cogs"></i> Manual</span>
                                <span><i class="fas fa-user-friends"></i> 5 Pessoas</span>
                            </div>
                            <p class="carro-protecao"><i class="fas fa-check-circle"></i> Proteção do veículo e contra roubo</p>
                        </div>
                    </div>
                    
                    <div class="card-locais">
                        <h3>Locais e Horários</h3>
                        
                        <div class="local-info">
                            <p><strong><i class="fas fa-arrow-up" style="color: #00bfff; margin-right: 8px;"></i>RETIRADA</strong></p>
                            <input type="datetime-local" id="input-dt-retirada" class="entrada-formulario" style="width: 100%; margin-bottom: 8px;">
                            <input type="text" id="input-loc-retirada" placeholder="Local de Retirada (Ex: Aeroporto, Endereço)" class="entrada-formulario" value="Aeroporto" style="width: 100%;">
                            <p class="servico-shuttle"><i class="fas fa-bus"></i> Serviço de Shuttle</p>
                        </div>
                        
                        <div class="local-info">
                            <p><strong><i class="fas fa-arrow-down" style="color: #cc0000; margin-right: 8px;"></i>DEVOLUÇÃO</strong></p>
                            <input type="datetime-local" id="input-dt-devolucao" class="entrada-formulario" style="width: 100%; margin-bottom: 8px;">
                            <input type="text" id="input-loc-devolucao" placeholder="Local de Devolução" class="entrada-formulario" value="Aeroporto" style="width: 100%;">
                            <p class="servico-shuttle"><i class="fas fa-bus"></i> Serviço de Shuttle</p>
                        </div>
                        
                        <button class="btn-alterar">Alterar Locais/Horários</button>
                    </div>
                </div>
            </div>

            <div class="coluna-direita">
                <div class="resumo-reserva">
                    <div class="reserva-header">
                        <h2>Sua Reserva</h2>
                        <span class="dias-badge" id="reserva-dias">0 dias</span>
                    </div>

                    <div class="valores-resumo">
                        <div class="linha-valor">
                            <span>Valor do Veículo</span>
                            <strong id="valor-veiculo-resumo">R$ 0,00</strong>
                        </div>
                        <div class="linha-valor">
                            <span>Seguro (obrigatório)</span>
                            <strong id="valor-seguro-resumo">R$ 0,00</strong>
                        </div>
                        <div class="linha-valor">
                            <span>Taxa da Locadora</span>
                            <strong id="valor-taxa-resumo">R$ 0,00</strong>
                        </div>
                        
                        <div class="linha-valor total">
                            <span>Valor Total:</span>
                            <strong id="valor-total-resumo">R$ 0,00</strong>
                        </div>
                    </div>

                    <button class="btn-pagar" id="btn-abrir-modal-pagamento">PAGAR</button>
                    <p id="pagamento-msg-main" style="margin-top:10px;text-align:center;font-weight:bold;"></p>
                </div>
            </div>
        </div>
    </div>

    <div id="modalPagamento" class="modal-pagamento-container" onclick="if(event.target.id === 'modalPagamento') this.style.display='none'">
        <div class="modal-content-pagamento" onclick="event.stopPropagation()">
            <span class="fechar" onclick="document.getElementById('modalPagamento').style.display='none'">&times;</span>
            <h2>Escolha o Método de Pagamento</h2>
            
            <div class="opcoes-pagamento">
                <button id="btn-metodo-pix" data-metodo="Pix">PIX</button>
                <button id="btn-metodo-cartao" data-metodo="Cartão Crédito">CARTÃO</button>
            </div>
            
            <p id="msg-status" style="margin-top: 15px; font-weight: bold;"></p>
            <button id="btn-finalizar-compra" class="btn-confirmar" style="display:none;">Confirmar Pagamento</button>
            
        </div>
    </div>

    <script src="../../public/js/auth-header.js"></script>
    <script src="../../public/js/pagamento.js"></script>
</body>
</html>