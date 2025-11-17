document.addEventListener("DOMContentLoaded", () => {
  // Buscar dados da URL ao invés do localStorage
  const urlParams = new URLSearchParams(window.location.search);
  let reservaData = null;

  const el = {
    feedbackErro: document.getElementById("feedback-erro-resumo"),
    carroImg: document.getElementById("carro-img-resumo"),
    carroModelo: document.getElementById("carro-modelo-resumo"),
    carroCategoria: document.getElementById("carro-categoria-resumo"),
    inputDtRetirada: document.getElementById("input-dt-retirada"),
    inputDtDevolucao: document.getElementById("input-dt-devolucao"),
    inputLocRetirada: document.getElementById("input-loc-retirada"),
    inputLocDevolucao: document.getElementById("input-loc-devolucao"),
    reservaDias: document.getElementById("reserva-dias"),
    valorTotal: document.getElementById("valor-total-resumo"),
    btnAbrirModal: document.getElementById("btn-abrir-modal-pagamento"),
    modal: document.getElementById("modalPagamento"),
    btnMetodoPix: document.getElementById("btn-metodo-pix"),
    btnMetodoCartao: document.getElementById("btn-metodo-cartao"),
    btnFinalizarCompra: document.getElementById("btn-finalizar-compra"),
    msgStatus: document.getElementById("msg-status"),
    msgPagamentoMain: document.getElementById("pagamento-msg-main"),
    metodoSelecionado: "Pix",
  };

  const IMAGES_BASE = "/AV2DAW/public/images/uploads/carros/";

  function formatarMoeda(valor) {
    return parseFloat(valor).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  }

  function converterDataParaInputDatetime(d) {
    const pad = (n) => String(n).padStart(2, "0");
    const ano = d.getFullYear();
    const mes = pad(d.getMonth() + 1);
    const dia = pad(d.getDate());
    const hora = pad(d.getHours());
    const minuto = pad(d.getMinutes());
    return `${ano}-${mes}-${dia}T${hora}:${minuto}`;
  }

  function preencherTela() {
    if (!reservaData) return;

    const valorTotal = parseFloat(reservaData.total) || 0;

    el.carroImg.src = IMAGES_BASE + (reservaData.imagem || "default.png");
    el.carroImg.alt = reservaData.nome_veiculo;
    el.carroModelo.textContent = reservaData.nome_veiculo;
    el.carroCategoria.textContent = reservaData.categoria;

    if (el.inputDtRetirada)
      el.inputDtRetirada.value = reservaData.data_retirada;
    if (el.inputDtDevolucao)
      el.inputDtDevolucao.value = reservaData.data_devolucao;
    if (el.inputLocRetirada)
      el.inputLocRetirada.value = reservaData.local_retirada;
    if (el.inputLocDevolucao)
      el.inputLocDevolucao.value = reservaData.local_devolucao;

    el.reservaDias.textContent = `${reservaData.dias} ${
      reservaData.dias > 1 ? "dias" : "dia"
    }`;
    el.valorTotal.textContent = formatarMoeda(valorTotal);
  }

  async function finalizarPagamento() {
    if (!reservaData || !el.metodoSelecionado) return;

    el.btnFinalizarCompra.disabled = true;
    el.msgStatus.textContent = "Processando pagamento...";
    el.msgStatus.style.color = "gray";

    const payload = {
      id_veiculo: Number(reservaData.id_veiculo),
      valor_total: Number(reservaData.total),
      dias: Number(reservaData.dias),
      metodo_pagamento: el.metodoSelecionado,
      local_retirada: el.inputLocRetirada ? el.inputLocRetirada.value : "",
      local_devolucao: el.inputLocDevolucao ? el.inputLocDevolucao.value : "",
      data_retirada: el.inputDtRetirada ? el.inputDtRetirada.value : "",
      data_devolucao: el.inputDtDevolucao ? el.inputDtDevolucao.value : "",
    };

    try {
      const response = await fetch(
        "../../public/api/client/criar-locacao.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        }
      );

      const result = await response.json();

      if (result.success) {
        el.msgStatus.textContent = "Pagamento aprovado! Redirecionando...";
        el.msgStatus.style.color = "green";

        localStorage.setItem(
          "locacao_finalizada",
          JSON.stringify({
            ...reservaData,
            id_locacao: result.id_locacao,
          })
        );
        localStorage.removeItem("alucar_booking");

        el.msgPagamentoMain.textContent = "Reserva confirmada. Aguarde...";
        el.msgPagamentoMain.style.color = "green";
        if (el.modal) el.modal.style.display = "none";

        setTimeout(() => {
          // CORREÇÃO AQUI: Passa o id_locacao na URL
          window.location.href = `checkout.php?id_locacao=${result.id_locacao}`;
        }, 1500);
      } else {
        el.msgStatus.textContent =
          result.error || "Erro no pagamento. Tente novamente.";
        el.msgStatus.style.color = "red";
        if (el.btnFinalizarCompra) el.btnFinalizarCompra.disabled = false;
      }
    } catch (error) {
      console.error("Erro na comunicação com a API de locação:", error);
      el.msgStatus.textContent = "Erro de rede ou servidor. Tente novamente.";
      el.msgStatus.style.color = "red";
      if (el.btnFinalizarCompra) el.btnFinalizarCompra.disabled = false;
    }
  }

  try {
    // Pegar dados da URL
    reservaData = {
      id_veiculo: urlParams.get("id_veiculo"),
      nome_veiculo: urlParams.get("nome_veiculo"),
      categoria: urlParams.get("categoria"),
      transmissao: urlParams.get("transmissao"),
      capacidade: urlParams.get("capacidade"),
      imagem: urlParams.get("imagem"),
      data_retirada: urlParams.get("data_retirada"),
      data_devolucao: urlParams.get("data_devolucao"),
      local_retirada: urlParams.get("local_retirada"),
      local_devolucao: urlParams.get("local_devolucao"),
      dias: urlParams.get("dias"),
      preco_diaria: urlParams.get("preco_diaria"),
      subtotal: urlParams.get("subtotal"),
      seguro: urlParams.get("seguro"),
      tem_seguro: urlParams.get("tem_seguro"),
      taxa: urlParams.get("taxa"),
      total: urlParams.get("total"),
    };

    if (!reservaData.id_veiculo)
      throw new Error("Dados de reserva incompletos.");
    preencherTela();
  } catch (e) {
    el.feedbackErro.textContent =
      "Ops! Reserva não encontrada. Volte à página de seleção.";
    if (el.btnAbrirModal) el.btnAbrirModal.disabled = true;
    return;
  }

  if (el.btnAbrirModal)
    el.btnAbrirModal.addEventListener("click", () => {
      if (el.modal) el.modal.style.display = "flex";
      if (el.msgStatus) {
        el.msgStatus.textContent = "Selecione um método de pagamento.";
        el.msgStatus.style.color = "#333";
      }
      if (el.btnFinalizarCompra) {
        el.btnFinalizarCompra.style.display = "none";
        el.btnFinalizarCompra.disabled = false;
      }
    });

  const handleMetodoClick = (e) => {
    const btn = e.target.closest("button");
    if (!btn) return;
    const metodo = btn.dataset.metodo;
    if (metodo) {
      el.metodoSelecionado = metodo;
      if (el.msgStatus) {
        el.msgStatus.textContent = `Você selecionou ${metodo}.`;
        el.msgStatus.style.color = "#00bfff";
      }
      const valorTotal = parseFloat(reservaData.total) || 0;
      if (el.btnFinalizarCompra) {
        el.btnFinalizarCompra.textContent = `Pagar ${formatarMoeda(
          valorTotal
        )}`;
        el.btnFinalizarCompra.style.display = "block";
      }
    }
    if (el.btnMetodoPix) el.btnMetodoPix.classList.remove("selected");
    if (el.btnMetodoCartao) el.btnMetodoCartao.classList.remove("selected");
    btn.classList.add("selected");
  };

  if (el.btnMetodoPix)
    el.btnMetodoPix.addEventListener("click", handleMetodoClick);
  if (el.btnMetodoCartao)
    el.btnMetodoCartao.addEventListener("click", handleMetodoClick);
  if (el.btnFinalizarCompra)
    el.btnFinalizarCompra.addEventListener("click", finalizarPagamento);
});
