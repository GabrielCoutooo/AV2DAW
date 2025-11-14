document.addEventListener('DOMContentLoaded', async () => {
    // 1. Elementos de destino
    const frotaGrid = document.getElementById('frota-grid');
    const vendedoresTabela = document.getElementById('vendedores-tabela');
    const checklistsTabela = document.getElementById('checklists-tabela');
    const estatisticasGrid = document.getElementById('estatisticas-grid');

    // 2. Requisição à API (Ajuste o caminho conforme onde você colocou dashboard_data.php)
    try {
        const response = await fetch('../../api/adm/dashboard_data.php'); 
        
        if (!response.ok) {
            // Trata o erro (ex: 401 Unauthorized)
            const errorData = await response.json();
            alert(`Erro ao carregar dados: ${errorData.error || response.statusText}`);
            // Limpar conteúdo e mostrar erro
            frotaGrid.innerHTML = `<p style="color: red;">${errorData.error || 'Erro ao carregar dashboard.'}</p>`;
            return; 
        }

        const data = await response.json();
        
        // 3. Renderização dos dados
        renderFrota(data.veiculos, frotaGrid);
        renderVendedores(data.vendedores, vendedoresTabela);
        renderChecklists(data.checklists, checklistsTabela);
        renderEstatisticas(data.estatisticas, estatisticasGrid);

    } catch (error) {
        console.error('Erro na comunicação com o backend:', error);
        frotaGrid.innerHTML = `<p style="color: red;">Erro de rede ou servidor.</p>`;
    }
});

function renderFrota(veiculos, container) {
    container.innerHTML = ''; 
    veiculos.forEach(veiculo => {
        const disponivelText = veiculo.disponivel ? 'Disponível' : 'Indisponível';
        const disponivelColor = veiculo.disponivel ? '#27ae60' : '#e74c3c';

        const card = `
            <div class="veiculo-card" onclick="verDetalhesVeiculo(${veiculo.id})">
                <img src="${veiculo.imagem}" alt="${veiculo.modelo}" class="veiculo-img">
                <h3>${veiculo.marca} ${veiculo.modelo}</h3>
                <p>${veiculo.categoria}</p>
                <span style="color: ${disponivelColor};">
                    ${disponivelText}
                </span>
            </div>
        `;
        container.innerHTML += card;
    });
}

function renderVendedores(vendedores, container) {
    container.innerHTML = '';
    vendedores.forEach(vendedor => {
        const row = `
            <tr>
                <td>${vendedor.nome}</td>
                <td>${vendedor.contato}</td>
                <td>${vendedor.turno}</td>
                <td>${vendedor.ultimo_modelo}</td>
                <td>
                    <button class="btn btn-primary">VER MAIS</button>
                    <button class="btn" style="background-color: #e74c3c; color: white;">EXCLUIR</button>
                </td>
            </tr>
        `;
        container.innerHTML += row;
    });
}

function renderChecklists(checklists, container) {
    container.innerHTML = '';
    checklists.forEach(checklist => {
        const row = `
            <tr>
                <td>${checklist.doc_cliente}</td>
                <td>${checklist.modelo}</td>
                <td>${checklist.data}</td>
                <td>${checklist.tipo}</td>
                <td>
                    <button class="btn btn-success" onclick="verChecklist('${checklist.doc_cliente}', '${checklist.data}')">
                        Abrir Checklist
                    </button>
                </td>
            </tr>
        `;
        container.innerHTML += row;
    });
}

function renderEstatisticas(stats, container) {
    container.innerHTML = '';
    // Mapeia os dados para o formato HTML de card
    const cards = [
        { title: 'CARROS ALUGADOS', number: stats.carros_alugados, subtitle: 'Atualmente' },
        { title: 'CARROS DISPONÍVEIS', number: stats.carros_disponiveis, subtitle: 'Para locação' },
        { title: 'EM MANUTENÇÃO', number: stats.carros_manutencao, subtitle: 'Veículos' },
        { title: 'VENDAS MÊS', number: stats.vendas_mes, subtitle: 'Últimos 30 dias' }
    ];

    cards.forEach(card => {
        const htmlCard = `
            <div class="stat-card">
                <h3>${card.title}</h3>
                <div class="stat-number">${card.number}</div>
                <p>${card.subtitle}</p>
            </div>
        `;
        container.innerHTML += htmlCard;
    });
}

// Manter as funções originais do index.php que faziam redirecionamentos/modais,
// pois elas ainda são necessárias no frontend:
function verDetalhesVeiculo(id) {
    window.location.href = 'gerenciar_veiculo.php?id=' + id;
}

function verChecklist(docCliente, data) {
    window.location.href = 'checklist.php?doc=' + docCliente + '&data=' + data;
}

function abrirModalCadastro() {
    document.getElementById('modalCadastro').style.display = 'block';
}

function fecharModalCadastro() {
    document.getElementById('modalCadastro').style.display = 'none';
}

// Fechar modal ao clicar fora (mantido do original)
document.getElementById('modalCadastro').addEventListener('click', function(e) {
    if (e.target.id === 'modalCadastro') {
        fecharModalCadastro();
    }
});