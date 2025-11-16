
<?php
include_once 'header.php';
require_once __DIR__ . "/../../config/auth-check.php";

if (!adminEstaLogado()) {
    header("Location: login.html");
    exit;
}

$id_locacao = isset($_GET['id_locacao']) ? (int)$_GET['id_locacao'] : 0;
?>

<main>
    <section class="check-in">
        <h2>CHECK-IN / VISTORIA DE DEVOLUÇÃO</h2>
        <div id="loading" style="text-align: center; padding: 20px;">Carregando Locação #<?= $id_locacao ?>...</div>
        
        <div id="locacao-detalhes" style="display: none;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div>
                    <h3>Detalhes do Cliente</h3>
                    <p><strong>Nome:</strong> <span id="cliente-nome"></span></p>
                    <p><strong>CPF:</strong> <span id="cliente-cpf"></span></p>
                    <p><strong>Email:</strong> <span id="cliente-email"></span></p>
                </div>
                <div>
                    <h3>Detalhes do Veículo</h3>
                    <p><strong>Modelo:</strong> <span id="veiculo-modelo"></span></p>
                    <p><strong>Placa:</strong> <span id="veiculo-placa"></span></p>
                    <p><strong>Retirada:</strong> <span id="data-retirada"></span></p>
                    <p><strong>Devolução Prevista:</strong> <span id="data-devolucao"></span></p>
                </div>
            </div>
            
            <form id="form-checklist" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3>Checklist de Vistoria</h3>
                <p style="margin-bottom: 15px; color: #cc0000; font-weight: bold;">Preencha o checklist em caso de avarias ou inconsistências.</p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                    <label>Combustível:</label>
                    <select name="nivel_combustivel" required>
                        <option value="Cheio">Cheio</option>
                        <option value="Abaixo">Abaixo</option>
                    </select>

                    <label>Quilometragem Atual (km):</label>
                    <input type="number" name="quilometragem_atual" min="0" required>
                </div>

                <div id="checklist-itens-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 10px;">
                    </div>
                
                <h4 style="margin-top: 25px;">Observações (Ocorrências):</h4>
                <textarea name="avarias_registradas" rows="4" placeholder="Descreva avarias, multas ou outras ocorrências encontradas."></textarea>

                <div style="margin-top: 20px; display: flex; gap: 20px; align-items: center;">
                    <button type="button" class="btn btn-primary" onclick="finalizarCheckin('devolver')">
                        <i class="fas fa-check"></i> Finalizar Check-in (Sem Ocorrência)
                    </button>
                    <button type="button" class="btn btn-danger" onclick="finalizarCheckin('ocorrencia')">
                        <i class="fas fa-exclamation-triangle"></i> Registrar Vistoria & Ocorrência
                    </button>
                </div>
                <p id="checkin-mensagem" style="margin-top: 15px; color: red; font-weight: bold;"></p>
            </form>
        </div>
    </section>
</main>

<script>
    const ID_LOCACAO = <?= $id_locacao ?>;
    const API_DETALHES = '../../public/api/adm/locacao_detalhes.php';
    const API_CHECKIN = '../../public/api/adm/finalizar_checkin.php';

    // Itens padrão do checklist (inspirado em image_f013a2.jpg)
    const ITENS_CHECKLIST = [
        "Banco e Estofamento", "Painel e Controles", "Cintos de Segurança", 
        "Vidros e Espelhos", "Sistema de Som", "Ar Condicionado e Aquecedor",
        "Luzes Internas", "Limpeza Interna", "Lataria e Pintura", 
        "Faróis e Lanternas", "Pneus", "Para-brisa e Limpadores", 
        "Rodas e Aro", "Escapamento e Sistema de Exaustão", "Estrutura do Carro",
        "Nível de Óleo e Fluido de Motor", "Freios", "Suspensão", 
        "Bateria", "Embreagem e Câmbio", "Direção", "Combustível", "Condições de Limpeza"
    ];

    function formatarDataHora(dataString) {
        if (!dataString) return 'N/A';
        const dt = new Date(dataString.replace(' ', 'T'));
        return dt.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    }

    async function carregarDetalhesLocacao() {
        if (ID_LOCACAO === 0) {
            document.getElementById('loading').textContent = "Erro: ID da Locação não fornecido.";
            return;
        }

        try {
            const response = await fetch(`${API_DETALHES}?id_locacao=${ID_LOCACAO}`);
            const data = await response.json();

            if (!data.success) {
                document.getElementById('loading').textContent = `Erro ao carregar locação: ${data.error}`;
                return;
            }

            const loc = data.locacao;
            const cliente = data.cliente;
            const veiculo = data.veiculo;

            // Preenche detalhes do cliente
            document.getElementById('cliente-nome').textContent = cliente.nome || 'N/A';
            document.getElementById('cliente-cpf').textContent = cliente.cpf || 'N/A';
            document.getElementById('cliente-email').textContent = cliente.email || 'N/A';

            // Preenche detalhes do veículo/locação
            document.getElementById('veiculo-modelo').textContent = `${veiculo.marca} ${veiculo.nome_modelo}`;
            document.getElementById('veiculo-placa').textContent = veiculo.placa;
            document.getElementById('data-retirada').textContent = formatarDataHora(loc.data_hora_retirada);
            document.getElementById('data-devolucao').textContent = formatarDataHora(loc.data_hora_prevista_devolucao);

            // Renderiza itens do checklist
            const container = document.getElementById('checklist-itens-container');
            container.innerHTML = ITENS_CHECKLIST.map(item => `
                <label style="display: block; margin-bottom: 5px;">
                    <input type="checkbox" name="item_${item.toLowerCase().replace(/\s/g, '_')}" checked>
                    ${item}
                </label>
            `).join('');

            // Mostra a interface e esconde o loader
            document.getElementById('loading').style.display = 'none';
            document.getElementById('locacao-detalhes').style.display = 'block';

        } catch (error) {
            document.getElementById('loading').textContent = "Erro de rede ao carregar detalhes.";
            console.error(error);
        }
    }

    async function finalizarCheckin(tipoAcao) {
        const mensagemEl = document.getElementById('checkin-mensagem');
        mensagemEl.textContent = "Processando...";
        
        // Coleta dados da vistoria
        const form = document.getElementById('form-checklist');
        const formData = new FormData(form);
        
        // Coleta status dos itens do checklist
        const itensOk = [];
        const itensNaoOk = [];
        ITENS_CHECKLIST.forEach(item => {
            const name = `item_${item.toLowerCase().replace(/\s/g, '_')}`;
            if (formData.has(name)) {
                itensOk.push(item);
            } else {
                itensNaoOk.push(item);
            }
            formData.delete(name); // Remove checkbox do payload final
        });
        
        // Adiciona dados necessários para a API
        formData.append('id_locacao', ID_LOCACAO);
        formData.append('acao', tipoAcao);
        formData.append('itens_ok', itensOk.join('; '));
        formData.append('itens_nao_ok', itensNaoOk.join('; '));
        
        try {
            const response = await fetch(API_CHECKIN, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                mensagemEl.style.color = 'green';
                mensagemEl.textContent = result.message || 'Check-in realizado com sucesso!';
                alert(result.message || 'Check-in concluído! Redirecionando para o Dashboard.');
                window.location.href = 'index.php'; // Volta para o dashboard
            } else {
                mensagemEl.style.color = 'red';
                mensagemEl.textContent = result.error || 'Erro ao finalizar check-in.';
            }

        } catch (error) {
            mensagemEl.style.color = 'red';
            mensagemEl.textContent = 'Erro de comunicação com o servidor.';
            console.error(error);
        }
    }

    document.addEventListener('DOMContentLoaded', carregarDetalhesLocacao);
</script>