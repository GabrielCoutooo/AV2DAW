<?php
// Resolve o caminho de config de forma robusta usando __DIR__ (sobe 2 níveis até AV2DAW/)
$configPath = realpath(__DIR__ . '/../../config/config.php');
$altPath = __DIR__ . '/../../../config/config.php'; // fallback caso a estrutura seja diferente

if ($configPath && file_exists($configPath)) {
    require_once $configPath;
} elseif (file_exists($altPath)) {
    require_once $altPath;
} else {
    // Registra erro sem imprimir HTML — permite a página carregar para depuração visual
    error_log('AV2DAW: config.php não encontrado. Paths testados: ' . (__DIR__ . '/../../config/config.php') . ' , ' . $altPath);
    // opcional: você pode exibir uma mensagem curta no HTML durante o desenvolvimento
    // echo '<!-- config.php não encontrado -->';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alugar Veículo - ALUCAR</title>
    <link rel="icon" href="../../public/images/logosemfundo.png" type="image/png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Estilos adicionais específicos para a página de venda (DO PROTÓTIPO) */
        .container-venda {
            background-color: #f4f4f4;
            padding: 50px 20px;
            min-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card-venda {
            background-color: #fff;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            border: 5px solid #00bfff;
        }

        .carro-info-principal {
            flex: 1;
            min-width: 300px;
        }

        .carro-imagem-container {
            width: 100%;
            height: 250px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .carro-imagem {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .carro-detalhes {
            flex: 1;
            min-width: 300px;
        }

        .detalhes-header h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 5px;
        }

        .detalhes-header p {
            font-size: 1.2rem;
            color: #999;
            margin-bottom: 20px;
        }

        .preco-bloco strong {
            font-size: 3rem;
            color: #cc0000;
        }

        .preco-bloco span {
            font-size: 1.5rem;
            color: #333;
            font-weight: 500;
        }

        .specs-venda {
            display: flex;
            gap: 25px;
            margin-top: 25px;
            margin-bottom: 30px;
            font-size: 1rem;
            color: #555;
            flex-wrap: wrap;
        }

        .specs-venda span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .specs-venda i {
            color: #00bfff;
        }

        .carro-caracteristicas {
            list-style: none;
            padding: 0;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .carro-caracteristicas li {
            font-size: 1rem;
            color: #555;
            margin-bottom: 8px;
        }

        .carro-caracteristicas i {
            color: #27ae60;
            margin-right: 10px;
        }

        .dias-opcoes {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-dias {
            background-color: #999;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-dias:hover {
            background-color: #777;
        }

        .btn-dias.active {
            background-color: #cc0000;
            box-shadow: 0 4px 10px rgba(204, 0, 0, 0.4);
        }

        .btn-alugue {
            background-color: #cc0000;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
            width: 100%;
            max-width: 300px;
        }

        .btn-alugue:hover {
            background-color: #ff0000;
            box-shadow: 0 6px 15px rgba(255, 0, 0, 0.3);
        }

        .mensagem-aluguel {
            margin-top: 15px;
            font-weight: bold;
            text-align: center;
        }

        @media (max-width: 768px) {
            .card-venda {
                flex-direction: column;
                padding: 20px;
            }

            .specs-venda {
                justify-content: space-around;
            }

            .dias-opcoes {
                justify-content: center;
            }

            .btn-alugue {
                max-width: 100%;
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
                <a href="#contato" class="nav-link">Contato</a>
            </nav>
            <div class="login-btn" id="user-auth-container">
                 <a href="login.html" class="nav-link btn-login"><i class="fa-regular fa-circle-user"></i> Login</a>
            </div>
        </div>
    </header>

    <div class="container-venda">
        <div class="card-venda" id="card-veiculo">
             <h2 id="mensagem-carregando" style="width:100%; text-align:center;">Carregando detalhes do veículo...</h2>
            
            <div class="carro-info-principal">
                <div class="carro-imagem-container">
                    <img id="veiculo-imagem" src="" alt="Imagem do Veículo" class="carro-imagem">
                </div>
                <div class="specs-venda" id="carro-specs-venda">
                    </div>
            </div>
            
            <div class="carro-detalhes">
                <div class="detalhes-header">
                    <h2 id="veiculo-categoria"></h2>
                    <p id="veiculo-marca-modelo"></p>
                    <div class="preco-bloco">
                        <strong id="preco-total">R$ 0,00</strong>
                        <span id="label-dias">/7 dias</span>
                    </div>
                </div>
                <ul class="carro-caracteristicas" id="caracteristicas-list">
                    <li><i class="fas fa-check-circle"></i> Proteção do veículo e contra roubo</li>
                    </ul>
                <p style="margin-top: 20px;"><i class="fas fa-info-circle" style="color:#00bfff;"></i> Acesse informações importantes!</p>
                
                <div class="dias-opcoes" id="dias-opcoes">
                    <button class="btn-dias active" data-dias="7">7 dias</button>
                    <button class="btn-dias" data-dias="15">15 dias</button>
                    <button class="btn-dias" data-dias="30">30 dias</button>
                   
                
                <button class="btn-alugue" id="btn-finalizar-aluguel">Alugue</button>
                <p id="mensagem-aluguel" class="mensagem-aluguel"></p>
            </div>
        </div>
    </div>
    
    <script>
        // torna id_veiculo disponível para o JS
        const QUERY = new URLSearchParams(location.search);
        const ID_VEICULO = QUERY.get('id_veiculo') || '';
    </script>
    <script src="../../public/js/auth-header.js"></script>
    <script src="../../public/js/venda.js"></script>
</body>
</html>