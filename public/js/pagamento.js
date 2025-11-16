document.addEventListener('DOMContentLoaded', () => {
    // Carrega os dados salvos do localStorage na chave 'alucar_booking'
    const dadosReservaRaw = localStorage.getItem('alucar_booking');
    let reservaData = null;

    // Elementos da nova estrutura
    const el = {
        feedbackErro: document.getElementById('feedback-erro-resumo'),
        // Detalhes do Carro
        carroImg: document.getElementById('carro-img-resumo'),
        carroModelo: document.getElementById('carro-modelo-resumo'),
        carroCategoria: document.getElementById('carro-categoria-resumo'),
        
        // Inputs de Data/Local
        inputDtRetirada: document.getElementById('input-dt-retirada'),
        inputDtDevolucao: document.getElementById('input-dt-devolucao'),
        inputLocRetirada: document.getElementById('input-loc-retirada'),
        inputLocDevolucao: document.getElementById('input-loc-devolucao'),
        
        // Resumo de Dias/Valores
        reservaDias: document.getElementById('reserva-dias'),
        valorVeiculo: document.getElementById('valor-veiculo-resumo'),
        valorSeguro: document.getElementById('valor-seguro-resumo'),
        valorTaxa: document.getElementById('valor-taxa-resumo'),
        valorTotal: document.getElementById('valor-total-resumo'),
        
        // Modal e Botões
        btnAbrirModal: document.getElementById('btn-abrir-modal-pagamento'),
        modal: document.getElementById('modalPagamento'),
        btnMetodoPix: document.getElementById('btn-metodo-pix'),
        btnMetodoCartao: document.getElementById('btn-metodo-cartao'),
        btnFinalizarCompra: document.getElementById('btn-finalizar-compra'),
        msgStatus: document.getElementById('msg-status'),
        msgPagamentoMain: document.getElementById('pagamento-msg-main'),
        
        metodoSelecionado: 'Pix',
    };

    const IMAGES_BASE = '/AV2DAW/public/images/uploads/carros/';

    // Funções de Utilidade
    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });
    }

    /**
     * Converte um objeto Date para o formato YYYY-MM-DDTHH:MM, necessário para input[type="datetime-local"].
     * @param {Date} d - Objeto Date.
     * @returns {string} String no formato do input.
     */
    function converterDataParaInputDatetime(d) {
        const pad = (n) => String(n).padStart(2, '0');
        const ano = d.getFullYear();
        const mes = pad(d.getMonth() + 1);
        const dia = pad(d.getDate());
        const hora = pad(d.getHours());
        const minuto = pad(d.getMinutes());
        return `${ano}-${mes}-${dia}T${hora}:${minuto}`;
    }

    /**
     * Preenche os elementos da tela com os dados da reserva.
     * CORRIGIDO: Converte strings para float para evitar NaN
     */
    function preencherTela() {
        if (!reservaData) return;

        // Converte strings do localStorage para float - CORREÇÃO DO NaN
        const valorVeiculo = parseFloat(reservaData.valor_veiculo) || 0;
        const valorSeguro = parseFloat(reservaData.seguro) || 0;
        const valorTaxa = parseFloat(reservaData.taxa) || 0;
        const valorTotal = parseFloat(reservaData.valor_total) || 0;
        
        // Simulação de datas
        const dataRetirada = new Date(); 
        const dataDevolucao = new Date();
        dataDevolucao.setDate(dataRetirada.getDate() + parseInt(reservaData.dias));

        // Lado esquerdo: Detalhes do carro
        el.carroImg.src = IMAGES_BASE + (reservaData.imagem || 'default.png');
        el.carroImg.alt = reservaData.nome_modelo;
        el.carroModelo.textContent = `${reservaData.marca || ''} ${reservaData.nome_modelo}`;
        el.carroCategoria.textContent = reservaData.categoria;

        // Inputs de Data/Hora
        if(el.inputDtRetirada) el.inputDtRetirada.value = converterDataParaInputDatetime(dataRetirada);
        if(el.inputDtDevolucao) el.inputDtDevolucao.value = converterDataParaInputDatetime(dataDevolucao);
        
        // Lado direito: Resumo de valores - AGORA SEM NaN
        el.reservaDias.textContent = `${reservaData.dias} dias`;
        
        el.valorVeiculo.textContent = formatarMoeda(valorVeiculo);
        el.valorSeguro.textContent = formatarMoeda(valorSeguro);
        el.valorTaxa.textContent = formatarMoeda(valorTaxa);
        el.valorTotal.textContent = formatarMoeda(valorTotal);
    }

    /**
     * Lida com a finalização do pagamento via API.
     */
    async function finalizarPagamento() {
        if (!reservaData || !el.metodoSelecionado) return;

        el.btnFinalizarCompra.disabled = true;
        el.msgStatus.textContent = "Processando pagamento...";
        el.msgStatus.style.color = "gray";

        const payload = {
            id_veiculo: Number(reservaData.id_veiculo),
            valor_total: Number(reservaData.valor_total),
            dias: Number(reservaData.dias),
            metodo_pagamento: el.metodoSelecionado,
        };

        try {
            const response = await fetch('../../public/api/client/criar-locacao.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json(); 

            if (result.success) {
                el.msgStatus.textContent = "Pagamento aprovado! Redirecionando...";
                el.msgStatus.style.color = "green";
                
                localStorage.setItem('locacao_finalizada', JSON.stringify({
                    ...reservaData,
                    id_locacao: result.id_locacao,
                }));
                localStorage.removeItem('alucar_booking');
                
                el.msgPagamentoMain.textContent = 'Reserva confirmada. Aguarde...';
                el.msgPagamentoMain.style.color = 'green';
                el.modal.style.display = 'none';

                setTimeout(() => {
                    window.location.href = `checkout.php`; 
                }, 1500);

            } else {
                el.msgStatus.textContent = result.error || "Erro no pagamento. Tente novamente.";
                el.msgStatus.style.color = "red";
                el.btnFinalizarCompra.disabled = false;
            }
        } catch (error) {
            console.error("Erro na comunicação com a API:", error);
            el.msgStatus.textContent = "Erro de rede ou servidor. Tente novamente.";
            el.msgStatus.style.color = "red";
            el.btnFinalizarCompra.disabled = false;
        }
    }

    // Inicialização
    try {
        reservaData = JSON.parse(dadosReservaRaw);
        if (!reservaData || !reservaData.id_veiculo) {
            throw new Error("Dados de reserva incompletos.");
        }
        preencherTela();
    } catch (e) {
        el.feedbackErro.textContent = "Ops! Reserva não encontrada. Volte à página de seleção.";
        if(el.btnAbrirModal) el.btnAbrirModal.disabled = true;
        return;
    }
    
    // Botão Abrir Modal
    if(el.btnAbrirModal) el.btnAbrirModal.addEventListener('click', () => {
        if(el.modal) el.modal.style.display = 'flex';
        el.msgStatus.textContent = 'Selecione um método de pagamento.';
        el.msgStatus.style.color = '#333';
        el.btnFinalizarCompra.style.display = 'none';
        el.btnFinalizarCompra.disabled = false;
    });

    // Seleção de método
    const handleMetodoClick = (e) => {
        const btn = e.target.closest('button');
        const metodo = btn.dataset.metodo;
        if (metodo) {
            el.metodoSelecionado = metodo;
            el.msgStatus.textContent = `Você selecionou ${metodo}.`;
            el.msgStatus.style.color = '#00bfff';
            
            // Utiliza o valor corrigido do reservaData
            const valorTotal = parseFloat(reservaData.valor_total) || 0;
            el.btnFinalizarCompra.textContent = `Pagar ${formatarMoeda(valorTotal)}`;
            el.btnFinalizarCompra.style.display = 'block';
        }

        el.btnMetodoPix.classList.remove('selected');
        el.btnMetodoCartao.classList.remove('selected');
        btn.classList.add('selected');
    };

    if(el.btnMetodoPix) el.btnMetodoPix.addEventListener('click', handleMetodoClick);
    if(el.btnMetodoCartao) el.btnMetodoCartao.addEventListener('click', handleMetodoClick);

    // Botão Finalizar Compra
    if(el.btnFinalizarCompra) el.btnFinalizarCompra.addEventListener('click', finalizarPagamento);
});