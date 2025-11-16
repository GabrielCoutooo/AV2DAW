document.addEventListener("DOMContentLoaded", () => {
    let veiculoData = null;

    const el = {
        cardVeiculo: document.getElementById("card-veiculo"),
        imagem: document.getElementById('veiculo-imagem'),
        categoria: document.getElementById('veiculo-categoria'),
        marcaModelo: document.getElementById('veiculo-marca-modelo'),
        precoTotal: document.getElementById('preco-total'),
        labelDias: document.getElementById('label-dias'),
        specsVenda: document.getElementById('carro-specs-venda'),
        caracteristicasList: document.getElementById('caracteristicas-list'),
        diasOpcoes: document.getElementById('dias-opcoes'),
        diasInput: document.getElementById('dias-personalizados'),
        btnAlugar: document.getElementById('btn-finalizar-aluguel'),
        mensagem: document.getElementById('mensagem-aluguel'),
    };

    const IMAGES_BASE = '/AV2DAW/public/images/uploads/carros/';

    function getVeiculoIdFromUrl() {
        return ID_VEICULO ? parseInt(ID_VEICULO) : 0;
    }

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });
    }

    function atualizarPrecoTotal(dias) {
        if (!veiculoData) return;

        const precoDiaria = parseFloat(veiculoData.preco_diaria_base) || 0;
        let precoTotal = precoDiaria * dias;
        let label = `/${dias} dias`;

        if (dias >= 30) {
            precoTotal *= 0.9;
            label = "/mês (30 dias)";
        } else if (dias >= 15) {
            precoTotal *= 0.95;
            label = "/15 dias";
        } else if (dias === 7) {
            label = "/semana (7 dias)";
        }

        el.precoTotal.textContent = formatarMoeda(precoTotal);
        el.labelDias.textContent = label;
        el.btnAlugar.dataset.dias = dias;
        el.btnAlugar.dataset.total = precoTotal;
    }

    async function carregarDetalhesVeiculo() {
        const idVeiculo = getVeiculoIdFromUrl();
        const carregandoMsg = document.getElementById('mensagem-carregando');

        if (idVeiculo === 0) {
            if (carregandoMsg) carregandoMsg.textContent = "Erro: ID do veículo não encontrado na URL.";
            return;
        }

        if (el.cardVeiculo) el.cardVeiculo.style.opacity = '0.5';

        try {
            const response = await fetch(`../../public/api/obter-veiculo.php?id_veiculo=${idVeiculo}`);
            const data = await response.json();

            if (!data.success) {
                if (carregandoMsg) carregandoMsg.textContent = `Erro: ${data.error || "Veículo não encontrado."}`;
                return;
            }

            veiculoData = data.veiculo;
            if (el.cardVeiculo) el.cardVeiculo.style.opacity = '1';
            if (carregandoMsg) carregandoMsg.style.display = 'none';

            const imgFile = veiculoData.imagem ? veiculoData.imagem : 'default.png';
            el.imagem.src = IMAGES_BASE + imgFile;
            el.imagem.alt = `${veiculoData.marca} ${veiculoData.nome_modelo}`;

            el.categoria.textContent = veiculoData.categoria;
            el.marcaModelo.textContent = `${veiculoData.marca} ${veiculoData.nome_modelo}`;

            el.specsVenda.innerHTML = `
                <span><i class="fas fa-gas-pump"></i> 90L</span>
                <span><i class="fas fa-cogs"></i> ${veiculoData.tipo_transmissao || 'Não Informado'}</span>
                <span><i class="fas fa-user-friends"></i> ${veiculoData.capacidade_pessoas || '5'} Pessoas</span>
            `;

            el.caracteristicasList.innerHTML += `
                <li id="placa-li"><i class="fas fa-check-circle"></i> Placa: ${veiculoData.placa}</li>
                <li id="cor-li"><i class="fas fa-check-circle"></i> Cor: ${veiculoData.cor}</li>
            `;

            atualizarPrecoTotal(7);

            if (el.diasOpcoes) el.diasOpcoes.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-dias');
                if (btn) {
                    document.querySelectorAll('.btn-dias').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const diasSelecionados = parseInt(btn.dataset.dias);
                    if (el.diasInput) el.diasInput.value = '';
                    atualizarPrecoTotal(diasSelecionados);
                }
            });

            if (el.diasInput) el.diasInput.addEventListener('input', () => {
                const dias = parseInt(el.diasInput.value);
                if (dias > 0) {
                    document.querySelectorAll('.btn-dias').forEach(b => b.classList.remove('active'));
                    atualizarPrecoTotal(dias);
                }
            });

            if (el.btnAlugar) el.btnAlugar.addEventListener('click', () => {
                const dias = el.btnAlugar.dataset.dias || 7;
                const total = el.btnAlugar.dataset.total || 0;
                
                // salva no localStorage com a chave que pagamento.js espera
                const booking = {
                    id_veiculo: veiculoData.id_veiculo || veiculoData.id || ID_VEICULO,
                    dias: Number(dias),
                    valor_total: Number(total),
                    nome_modelo: veiculoData.nome_modelo || '',
                    marca: veiculoData.marca || '',
                    preco_diaria_base: veiculoData.preco_diaria_base || 0,
                    imagem: veiculoData.imagem || 'default.png'
                };
                try {
                    localStorage.setItem('alucar_booking', JSON.stringify(booking));
                } catch (e) {
                    console.warn('localStorage não disponível', e);
                }
                
                // redireciona para pagamento.php
                window.location.href = `/AV2DAW/views/client/pagamento.php`;
            });

        } catch (error) {
            console.error("Erro ao carregar veículo:", error);
            if (carregandoMsg) carregandoMsg.textContent = "Erro ao carregar os detalhes do veículo. Verifique o console para mais informações.";
        }
    }

    carregarDetalhesVeiculo();
});