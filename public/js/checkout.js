(function () {
  function obterParametroUrl(nomeParametro) {
    const parametrosUrl = new URLSearchParams(window.location.search);
    return parametrosUrl.get(nomeParametro);
  }

  function formatarMoedaBrasileira(numero) {
    return Number(numero || 0).toLocaleString("pt-BR", {
      style: "currency",
      currency: "BRL",
    });
  }

  function calcularDiferencaDias(dataInicio, dataFim) {
    const diferencaMilissegundos = dataFim - dataInicio;
    if (isNaN(diferencaMilissegundos)) return 1;
    const diasCalculados = Math.ceil(
      diferencaMilissegundos / (1000 * 60 * 60 * 24)
    );
    return Math.max(1, diasCalculados);
  }

  function converterDataParaInputDatetime(objetoData) {
    const adicionarZero = (numero) => String(numero).padStart(2, "0");
    const ano = objetoData.getFullYear();
    const mes = adicionarZero(objetoData.getMonth() + 1);
    const dia = adicionarZero(objetoData.getDate());
    const horas = adicionarZero(objetoData.getHours());
    const minutos = adicionarZero(objetoData.getMinutes());
    return `${ano}-${mes}-${dia}T${horas}:${minutos}`;
  }

  async function carregarDadosVeiculo() {
    const idVeiculo = obterParametroUrl("id");
    if (!idVeiculo) {
      alert("Veículo não informado.");
      window.location.href = "index.html";
      return;
    }
    try {
      const respostaApi = await fetch(
        `../../public/api/client/veiculo.php?id=${encodeURIComponent(
          idVeiculo
        )}`
      );
      const dadosResposta = await respostaApi.json();
      if (!respostaApi.ok || dadosResposta.error) {
        alert(dadosResposta.error || "Erro ao carregar veículo.");
        window.location.href = "index.html";
        return;
      }
      const dadosVeiculo = dadosResposta.veiculo;
      // Preenche cabeçalho do veículo
      document.getElementById(
        "vehicle-image"
      ).src = `/AV2DAW/public/images/uploads/carros/${
        dadosVeiculo.imagem || "default.png"
      }`;
      document.getElementById("vehicle-image").alt = dadosVeiculo.nome_modelo;
      document.getElementById("vehicle-name").textContent =
        dadosVeiculo.nome_modelo;
      document.getElementById("vehicle-category").textContent =
        dadosVeiculo.categoria || "";
      document.getElementById("spec-gear").textContent =
        dadosVeiculo.tipo_transmissao || "-";
      document.getElementById("spec-people").textContent =
        dadosVeiculo.capacidade_pessoas || "-";

      const precoDiariaBase = Number(dadosVeiculo.preco_diaria_base || 0);
      const taxaLocadora = 20.07; // exemplo fixo

      // Pré-preenche datas: retirada agora+2h, devolução +1 dia
      const dataAtual = new Date();
      dataAtual.setMinutes(0, 0, 0);
      const dataRetiradaPadrao = new Date(
        dataAtual.getTime() + 2 * 60 * 60 * 1000
      );
      const dataDevolucaoPadrao = new Date(
        dataRetiradaPadrao.getTime() + 24 * 60 * 60 * 1000
      );

      const inputDataRetirada = document.getElementById("pickup-dt");
      const inputDataDevolucao = document.getElementById("dropoff-dt");
      const inputLocalRetirada = document.getElementById("pickup-loc");
      const inputLocalDevolucao = document.getElementById("dropoff-loc");
      const badgeDiarias = document.getElementById("rent-days");
      const textoResumoQuantidadeDias = document.getElementById("summary-days");
      const textoDataRetirada = document.getElementById("pickup-date");
      const checkboxSeguro = document.getElementById("ins-opt");
      const inputQuantidadeDias = document.getElementById("days-input");
      const textoPrecoExibicao = document.getElementById("price-display");

      inputDataRetirada.value =
        converterDataParaInputDatetime(dataRetiradaPadrao);
      inputDataDevolucao.value =
        converterDataParaInputDatetime(dataDevolucaoPadrao);
      inputLocalRetirada.value = "Aeroporto";
      inputLocalDevolucao.value = "Aeroporto";
      checkboxSeguro.checked = true;
      if (inputQuantidadeDias)
        inputQuantidadeDias.value = String(
          calcularDiferencaDias(dataRetiradaPadrao, dataDevolucaoPadrao)
        );
      if (textoPrecoExibicao)
        textoPrecoExibicao.textContent =
          formatarMoedaBrasileira(precoDiariaBase);

      function recalcularTotais() {
        const dataRetiradaSelecionada = new Date(inputDataRetirada.value);
        const dataDevolucaoSelecionada = new Date(inputDataDevolucao.value);
        const quantidadeDiasCalculada = calcularDiferencaDias(
          dataRetiradaSelecionada,
          dataDevolucaoSelecionada
        );
        if (inputQuantidadeDias)
          inputQuantidadeDias.value = String(quantidadeDiasCalculada);

        const valorSubtotalVeiculo = precoDiariaBase * quantidadeDiasCalculada;
        const valorSeguro = checkboxSeguro.checked
          ? Math.round(valorSubtotalVeiculo * 0.091 * 100) / 100
          : 0;
        const valorTotalReserva =
          valorSubtotalVeiculo + valorSeguro + taxaLocadora;

        textoResumoQuantidadeDias.textContent =
          quantidadeDiasCalculada +
          (quantidadeDiasCalculada > 1 ? " dias" : " dia");
        badgeDiarias.textContent =
          quantidadeDiasCalculada +
          (quantidadeDiasCalculada > 1 ? " diárias" : " diária");
        document.getElementById("sum-vehicle").textContent =
          formatarMoedaBrasileira(valorSubtotalVeiculo);
        document.getElementById("sum-insurance").textContent =
          formatarMoedaBrasileira(valorSeguro);
        document.getElementById("sum-fee").textContent =
          formatarMoedaBrasileira(taxaLocadora);
        document.getElementById("sum-total").textContent =
          formatarMoedaBrasileira(valorTotalReserva);

        const dataRetiradaFormatada = dataRetiradaSelecionada.toLocaleString(
          "pt-BR",
          {
            dateStyle: "medium",
            timeStyle: "short",
          }
        );
        textoDataRetirada.textContent = `${dataRetiradaFormatada} • ${inputLocalRetirada.value}`;
      }

      ["change", "input"].forEach((tipoEvento) => {
        inputDataRetirada.addEventListener(tipoEvento, recalcularTotais);
        inputDataDevolucao.addEventListener(tipoEvento, recalcularTotais);
        inputLocalRetirada.addEventListener(tipoEvento, recalcularTotais);
        inputLocalDevolucao.addEventListener(tipoEvento, recalcularTotais);
        checkboxSeguro.addEventListener(tipoEvento, recalcularTotais);
      });

      if (inputQuantidadeDias) {
        // Quando usuário altera dias manualmente, atualiza data de devolução
        const aoAlterarQuantidadeDias = () => {
          const dataRetiradaAtual = new Date(inputDataRetirada.value);
          let diasSelecionados = parseInt(inputQuantidadeDias.value, 10);
          if (isNaN(diasSelecionados) || diasSelecionados < 1)
            diasSelecionados = 1;
          const novaDataDevolucao = new Date(
            dataRetiradaAtual.getTime() + diasSelecionados * 24 * 60 * 60 * 1000
          );
          inputDataDevolucao.value =
            converterDataParaInputDatetime(novaDataDevolucao);
          recalcularTotais();
        };
        inputQuantidadeDias.addEventListener("change", aoAlterarQuantidadeDias);
        inputQuantidadeDias.addEventListener("input", aoAlterarQuantidadeDias);
      }

      recalcularTotais();

      document.getElementById("btn-pay").addEventListener("click", () => {
        alert("Pagamento processado (demo).");
        window.location.href = "index.html";
      });
    } catch (erro) {
      console.error(erro);
      alert("Falha ao carregar dados do veículo.");
      window.location.href = "index.html";
    }
  }

  document.addEventListener("DOMContentLoaded", carregarDadosVeiculo);
})();
