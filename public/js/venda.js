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

    // REMOVENDO TODA A LÓGICA DE CÁLCULO DE TAXAS/SEGURO
    function atualizarPrecoTotal(dias) {
        if (!veiculoData) return;

        const precoDiaria = parseFloat(veiculoData.preco_diaria_base) || 0;
        let valorTotal = precoDiaria * dias;

        // Lógica de desconto (mantém, aplicada ao total)
        if (dias >= 30) {
            valorTotal *= 0.9; // 10% de desconto
        } else if (dias >= 15) {
            valorTotal *= 0.95; // 5% de desconto
        }
        
        let label = `/${dias} dias`;
        if (dias === 7) label = "/semana (7 dias)";
        if (dias >= 30) label = "/mês (30 dias)";

        el.precoTotal.textContent = formatarMoeda(valorTotal);
        el.labelDias.textContent = label;
        
        // Armazena no dataset (valor total apenas)
        el.btnAlugar.dataset.dias = dias;
        el.btnAlugar.dataset.total = valorTotal.toFixed(2);
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

            atualizarPrecoTotal(7); // Inicia com 7 dias

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
                const idVeiculo = veiculoData.id_veiculo || veiculoData.id || ID_VEICULO;
                const dias = Number(el.btnAlugar.dataset.dias);
                const total = el.btnAlugar.dataset.total;

                // PAYLOAD FINAL SALVO NO LOCAL STORAGE (APENAS COM VALOR TOTAL DO VEÍCULO)
                const booking = {
                    id_veiculo: Number(idVeiculo),
                    dias: Number(dias),
                    valor_total: total, 
                    nome_modelo: veiculoData.nome_modelo,
                    marca: veiculoData.marca,
                    categoria: veiculoData.categoria,
                    imagem: veiculoData.imagem,
                    preco_diaria_base: veiculoData.preco_diaria_base
                };
                try {
                    localStorage.setItem('alucar_booking', JSON.stringify(booking));
                } catch (e) {
                    console.warn('localStorage não disponível', e);
                }
                
                window.location.href = `/AV2DAW/views/client/pagamento.php`;
            });

        } catch (error) {
            console.error("Erro ao carregar veículo:", error);
            if (carregandoMsg) carregandoMsg.textContent = "Erro ao carregar os detalhes do veículo. Verifique o console para mais informações.";
        }
    }

    carregarDetalhesVeiculo();
});