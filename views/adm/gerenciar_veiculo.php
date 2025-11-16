 <?php
include_once 'header.php';
require_once __DIR__ . "/../../config/auth-check.php";

if (!adminEstaLogado()) {
    header("Location: login.html");
    exit;
}

$id_veiculo = isset($_GET['id_veiculo']) ? (int)$_GET['id_veiculo'] : 0;
?>

<main>
    <section class="gerenciar-veiculo">
        <h2>GERENCIAR VEÍCULO</h2>
        
        <?php if ($id_veiculo === 0): ?>
            <div style="padding: 20px; color: red;">
                Erro: ID do veículo não fornecido. Volte ao <a href="index.php">Dashboard</a>.
            </div>
        <?php else: ?>
            <div id="loading" style="text-align: center; padding: 20px;">
                Carregando detalhes do Veículo #<?= $id_veiculo ?>...
            </div>

            <div id="veiculo-detalhes" style="display: none;">
                
                <div class="detalhes-header-grid">
                    <div class="veiculo-info-card">
                        <h3 id="modelo-marca"></h3>
                        <p id="veiculo-placa-status"></p>
                        
                        <div class="info-grid">
                            <p><strong>Ano:</strong> <span id="info-ano"></span></p>
                            <p><strong>Cor:</strong> <span id="info-cor"></span></p>
                            <p><strong>Categoria:</strong> <span id="info-categoria"></span></p>
                            <p><strong>Diária Base:</strong> <span id="info-preco-diaria"></span></p>
                            <p><strong>Quilometragem:</strong> <span id="info-km"></span></p>
                            <p><strong>Transmissão:</strong> <span id="info-transmissao"></span></p>
                            <p><strong>Capacidade:</strong> <span id="info-pessoas"></span></p>
                        </div>

                        <div style="margin-top: 20px; display: flex; gap: 10px;">
                            <button class="btn btn-primary" onclick="abrirModalEdicaoDetalhes()">
                                <i class="fas fa-edit"></i> Editar Dados
                            </button>
                            <button class="btn btn-danger" onclick="excluirVeiculoDetalhes()">
                                <i class="fas fa-trash"></i> Excluir Veículo
                            </button>
                        </div>
                    </div>
                    
                    <div class="veiculo-imagem-container">
                        <img id="veiculo-imagem" src="" alt="Imagem do Veículo" class="veiculo-imagem-grande">
                    </div>
                </div>

                <h3 style="margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Histórico de Revisões</h3>
                <table id="revisoes-tabela">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Quilometragem</th>
                            <th>Descrição</th>
                            <th>Custo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="4" style="text-align: center;">Nenhuma revisão cadastrada.</td></tr>
                    </tbody>
                </table>
                
                <h3 style="margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Últimas Locações</h3>
                <table id="locacoes-tabela">
                    <thead>
                        <tr>
                            <th>ID Locação</th>
                            <th>Cliente</th>
                            <th>Retirada</th>
                            <th>Devolução Real</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" style="text-align: center;">Nenhuma locação registrada.</td></tr>
                    </tbody>
                </table>
                
            </div>
        <?php endif; ?>
    </section>
</main>

<?php // include_once 'modal_edicao_veiculo.php'; ?>


<style>
    .detalhes-header-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .veiculo-info-card h3 {
        font-size: 1.8rem;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    .veiculo-info-card p {
        margin: 5px 0;
        color: #555;
    }
    .veiculo-imagem-container {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f8f8;
        border-radius: 8px;
    }
    .veiculo-imagem-grande {
        max-width: 100%;
        max-height: 250px;
        object-fit: contain;
        padding: 10px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
    }
    #veiculo-placa-status {
        font-size: 1.1rem;
        font-weight: bold;
    }
    .status-disponivel { color: #27ae60; }
    .status-indisponivel { color: #e74c3c; }

    /* Estilos de tabela do header.php são aplicados aqui */
</style>

<script>
    const ID_VEICULO = <?= $id_veiculo ?>;
    const API_VEICULO = '../../public/api/obter-veiculo.php';
    const IMAGES_BASE = '/AV2DAW/public/images/uploads/carros/';
    
    // Simula a função de edição do adm_dashboard.js
    function abrirModalEdicaoDetalhes() {
        // Esta função deve ser implementada para abrir o modal de edição
        // Você precisará de uma nova API (ou adaptar a existente) para buscar todos os dados de edição,
        // mas por enquanto, alerte o ID.
        alert('Abrir modal de edição para ID: ' + ID_VEICULO + '\n(Função completa depende de importar o modal de edição.)');
        // Exemplo: window.abrirModalEdicao(window.currentVeiculoData);
    }
    
    // Simula a função de exclusão
    function excluirVeiculoDetalhes() {
        if (confirm(`Tem certeza que deseja EXCLUIR o veículo #${ID_VEICULO}?`)) {
            // Implementação da exclusão via AJAX (similar ao adm_dashboard.js)
            alert('Exclusão em andamento... (Implementar fetch)');
        }
    }

    function formatarData(d) {
        if (!d) return '-';
        const dt = new Date(d.replace(' ', 'T'));
        return dt.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    }

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });
    }

    async function carregarDadosVeiculo() {
        if (ID_VEICULO === 0) return;

        try {
            const response = await fetch(`${API_VEICULO}?id_veiculo=${ID_VEICULO}`);
            const data = await response.json();

            if (!data.success) {
                document.getElementById('loading').textContent = data.error || "Erro ao carregar veículo.";
                return;
            }

            const v = data.veiculo;
            window.currentVeiculoData = v; // Armazena para uso em modais
            
            const statusText = v.disponivel == 1 ? 'Disponível' : 'Indisponível';
            const statusClass = v.disponivel == 1 ? 'status-disponivel' : 'status-indisponivel';
            
            const imgPath = IMAGES_BASE + (v.imagem || 'default.png');

            // Preenchimento dos dados
            document.getElementById('modelo-marca').textContent = `${v.marca} ${v.nome_modelo}`;
            document.getElementById('veiculo-placa-status').innerHTML = `Placa: ${v.placa} | <span class="${statusClass}">${statusText}</span>`;
            
            document.getElementById('info-ano').textContent = v.ano;
            document.getElementById('info-cor').textContent = v.cor;
            document.getElementById('info-categoria').textContent = v.categoria;
            document.getElementById('info-preco-diaria').textContent = formatarMoeda(v.preco_diaria_base);
            document.getElementById('info-km').textContent = `${v.quilometragem_atual} km`;
            document.getElementById('info-transmissao').textContent = v.tipo_transmissao;
            document.getElementById('info-pessoas').textContent = v.capacidade_pessoas;
            
            document.getElementById('veiculo-imagem').src = imgPath;

            // Esconder loader e mostrar detalhes
            document.getElementById('loading').style.display = 'none';
            document.getElementById('veiculo-detalhes').style.display = 'block';

            // Nota: Históricos de Revisões e Locações exigem novas APIs, não implementadas aqui.
            // A tabela manterá a mensagem de "Nenhuma revisão/locação cadastrada."

        } catch (error) {
            document.getElementById('loading').textContent = "Erro de rede ao buscar detalhes do veículo.";
            console.error(error);
        }
    }

    document.addEventListener('DOMContentLoaded', carregarDadosVeiculo);
</script>
</body>
</html>