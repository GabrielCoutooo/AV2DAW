// ============ FUNÇÕES MODAIS DE CADASTRO ============
function abrirModalCadastro() {
  const modal = document.getElementById("modalCadastro");
  if (!modal) {
    console.error("Modal de cadastro não encontrado!");
    return;
  }
  document.getElementById("formCadastroVeiculo").reset();
  modal.style.display = "flex";
}

function fecharModalCadastro() {
  const modal = document.getElementById("modalCadastro");
  if (!modal) {
    console.error("Modal de cadastro não encontrado!");
    return;
  }
  modal.style.display = "none";
}

// ============ FUNÇÕES MODAIS DE EDIÇÃO ============
function abrirModalEdicao(veiculo) {
  console.log("abrirModalEdicao chamada com:", veiculo);

  const modal = document.getElementById("modalEdicao");
  if (!modal) {
    console.error("Modal de edição não encontrado!");
    return;
  }

  const form = document.getElementById("formEdicaoVeiculo");
  if (!form) {
    console.error("Formulário de edição não encontrado!");
    return;
  }

  // Limpar o formulário primeiro
  form.reset();

  // Preencher o campo hidden do id_veiculo PRIMEIRO e com certeza
  const idInput = form.querySelector('input[name="id_veiculo"]');
  // Corrigir aqui: usar id_veiculo do objeto
  const idVeiculo = veiculo.id_veiculo;
  console.log("ID Input element:", idInput);
  console.log("Tentando setar ID para:", idVeiculo);
  if (idInput) {
    idInput.value = String(idVeiculo);
    console.log("ID setado para:", idInput.value);
  } else {
    console.error("Campo id_veiculo não encontrado!");
  }

  // Preencher os outros campos
  const fields = [
    { selector: 'input[name="modelo"]', value: veiculo.modelo },
    { selector: 'input[name="marca"]', value: veiculo.marca },
    { selector: 'input[name="ano"]', value: veiculo.ano },
    { selector: 'select[name="categoria"]', value: veiculo.categoria },
    { selector: 'input[name="cor"]', value: veiculo.cor },
    { selector: 'input[name="placa"]', value: veiculo.placa },
    {
      selector: 'input[name="preco_diaria_base"]',
      value: veiculo.preco_diaria_base,
    },
  ];

  fields.forEach(({ selector, value }) => {
    const element = form.querySelector(selector);
    if (element) {
      element.value = value || "";
    }
  });

  // Setar o status
  const disponivelSelect = form.querySelector('select[name="disponivel"]');
  if (disponivelSelect) {
    disponivelSelect.value = veiculo.disponivel ? "1" : "0";
  }

  // Armazenar o ID do veículo sendo editado em uma variável global
  window.veiculoEditandoId = idVeiculo;

  modal.style.display = "flex";
}

function fecharModalEdicao() {
  const modal = document.getElementById("modalEdicao");
  if (!modal) {
    console.error("Modal de edição não encontrado!");
    return;
  }
  modal.style.display = "none";
  document.getElementById("formEdicaoVeiculo").reset();
  window.veiculoEditandoId = null;
}

function excluirVeiculo() {
  if (!window.veiculoEditandoId) {
    alert("ID do veículo não encontrado!");
    return;
  }

  if (
    confirm(
      "Tem certeza que deseja excluir este veículo? Esta ação não pode ser desfeita."
    )
  ) {
    const formData = new FormData();
    formData.append("acao", "deletar");
    formData.append("id_veiculo", window.veiculoEditandoId);

    fetch("/AV2DAW/views/adm/remover_veiculo.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          alert("Veículo excluído com sucesso!");
          fecharModalEdicao();
          document.dispatchEvent(new Event("atualizarDashboard"));
        } else {
          alert("Erro: " + result.error);
        }
      })
      .catch((err) => {
        console.error("Erro ao excluir:", err);
        alert("Erro ao excluir o veículo!");
      });
  }
}

// ============ EVENT LISTENERS PARA OS FORMULÁRIOS ============
document.addEventListener("DOMContentLoaded", () => {
  // Fechar modais ao clicar fora
  window.addEventListener("click", function (e) {
    const modalCadastro = document.getElementById("modalCadastro");
    const modalEdicao = document.getElementById("modalEdicao");

    if (e.target === modalCadastro) {
      fecharModalCadastro();
    }
    if (e.target === modalEdicao) {
      fecharModalEdicao();
    }
  });

  // Formulário de Cadastro
  const formCadastro = document.getElementById("formCadastroVeiculo");
  if (formCadastro) {
    formCadastro.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append("acao", "criar");

      try {
        const response = await fetch(
          "/AV2DAW/views/adm/cadastrar_veiculo.php",
          {
            method: "POST",
            body: formData,
          }
        );

        const texto = await response.text();
        console.log("RETORNO PHP:", texto);

        let result = {};
        try {
          result = JSON.parse(texto);
        } catch (e) {
          alert("ERRO: o PHP retornou algo que não é JSON.");
          return;
        }

        if (result.success) {
          alert("Veículo cadastrado com sucesso!");
          fecharModalCadastro();
          document.dispatchEvent(new Event("atualizarDashboard"));
        } else {
          alert("Erro: " + result.error);
        }
      } catch (err) {
        console.error("Erro ao enviar:", err);
        alert("Erro ao enviar o formulário!");
      }
    });
  }

  // Formulário de Edição
  const formEdicao = document.getElementById("formEdicaoVeiculo");
  if (formEdicao) {
    formEdicao.addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      formData.append("acao", "atualizar");

      // Debug: log dos dados
      console.log("Dados sendo enviados:");
      for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
      }

      try {
        const response = await fetch(
          "/AV2DAW/views/adm/cadastrar_veiculo.php",
          {
            method: "POST",
            body: formData,
          }
        );

        const texto = await response.text();
        console.log("RETORNO PHP:", texto);

        let result = {};
        try {
          result = JSON.parse(texto);
        } catch (e) {
          console.error("Erro ao fazer parse do JSON:", texto);
          alert(
            "ERRO: o PHP retornou algo que não é JSON.\nResposta: " + texto
          );
          return;
        }

        console.log("Resultado parseado:", result);

        if (result.success) {
          alert("Veículo atualizado com sucesso!");
          fecharModalEdicao();
          document.dispatchEvent(new Event("atualizarDashboard"));
        } else {
          alert(
            "Erro: " + (result.error || result.message || "Erro desconhecido")
          );
        }
      } catch (err) {
        console.error("Erro ao enviar:", err);
        alert("Erro ao atualizar o veículo!");
      }
    });
  }

  // Carrega os dados do dashboard
  carregarDashboard();
});

// ============ FUNÇÕES DE RENDERIZAÇÃO ============
function renderFrota(veiculos, container) {
  container.innerHTML = "";

  veiculos.forEach((veiculo) => {
    const disponivelText = veiculo.disponivel ? "Disponível" : "Indisponível";
    const disponivelColor = veiculo.disponivel ? "#27ae60" : "#e74c3c";

    const imagem =
      veiculo.imagem && veiculo.imagem.trim() !== ""
        ? veiculo.imagem
        : "default.png";
    const card = document.createElement("div");
    card.classList.add("veiculo-card");

    card.innerHTML = `
      <div class="veiculo-card-inner" onclick="verDetalhesVeiculo(${veiculo.id_veiculo})">
        <img src="${imagem}" alt="${veiculo.modelo}" class="veiculo-img">
        <h3>${veiculo.marca} ${veiculo.modelo}</h3>
        <p>${veiculo.categoria}</p>
        <span style="color: ${disponivelColor};">
          ${disponivelText}
        </span>
      </div>
      <button class="btn btn-primary btn-editar" data-id="${veiculo.id_veiculo}">Editar</button>
    `;
    card.querySelector(".btn-editar").addEventListener("click", (e) => {
      e.stopPropagation();
      abrirModalEdicao(veiculo);
    });

    container.appendChild(card);
  });
}

function renderVendedores(vendedores, container) {
  container.innerHTML = "";
  vendedores.forEach((vendedor) => {
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
  container.innerHTML = "";
  checklists.forEach((checklist) => {
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
  container.innerHTML = "";
  const cards = [
    {
      title: "CARROS ALUGADOS",
      number: stats.carros_alugados,
      subtitle: "Atualmente",
    },
    {
      title: "CARROS DISPONÍVEIS",
      number: stats.carros_disponiveis,
      subtitle: "Para locação",
    },
    {
      title: "EM MANUTENÇÃO",
      number: stats.carros_manutencao,
      subtitle: "Veículos",
    },
    {
      title: "VENDAS MÊS",
      number: stats.vendas_mes,
      subtitle: "Últimos 30 dias",
    },
  ];

  cards.forEach((card) => {
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

// ============ FUNÇÕES DE NAVEGAÇÃO ============
function verDetalhesVeiculo(id) {
  window.location.href = "gerenciar_veiculo.php?id_veiculo=" + id;
}

function verChecklist(docCliente, data) {
  window.location.href = "checklist.php?doc=" + docCliente + "&data=" + data;
}

// ============ FUNÇÕES DE CARREGAMENTO DO DASHBOARD ============
async function carregarDashboard() {
  const frotaGrid = document.getElementById("frota-grid");
  const vendedoresTabela = document.getElementById("vendedores-tabela");
  const checklistsTabela = document.getElementById("checklists-tabela");
  const estatisticasGrid = document.getElementById("estatisticas-grid");

  try {
    const response = await fetch("../../public/api/adm/dashboard_data.php");

    if (!response.ok) {
      const errorData = await response.json();
      alert(
        `Erro ao carregar dados: ${errorData.error || response.statusText}`
      );
      frotaGrid.innerHTML = `<p style="color: red;">${
        errorData.error || "Erro ao carregar dashboard."
      }</p>`;
      return;
    }

    const data = await response.json();

    renderFrota(data.veiculos, frotaGrid);
    renderVendedores(data.vendedores, vendedoresTabela);
    renderChecklists(data.checklists, checklistsTabela);
    renderEstatisticas(data.estatisticas, estatisticasGrid);
  } catch (error) {
    console.error("Erro na comunicação com o backend:", error);
    frotaGrid.innerHTML = `<p style="color: red;">Erro de rede ou servidor.</p>`;
  }
}

// Event listener para atualizar o dashboard
document.addEventListener("atualizarDashboard", carregarDashboard);
