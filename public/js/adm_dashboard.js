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

// ============ FUNÇÕES MODAIS DE VENDEDOR ============
function abrirModalCadastroVendedor() {
  const modal = document.getElementById("modalCadastroVendedor");
  if (!modal) return;
  document.getElementById("formCadastroVendedor").reset();
  document.getElementById("msg-cadastro-vendedor").textContent = '';
  modal.style.display = "flex";
}

function fecharModalCadastroVendedor() {
  const modal = document.getElementById("modalCadastroVendedor");
  if (!modal) return;
  modal.style.display = "none";
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

  // Formulário de Cadastro de Vendedor
  const formCadastroVendedor = document.getElementById("formCadastroVendedor");
  if (formCadastroVendedor) {
    formCadastroVendedor.addEventListener("submit", async function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const msgEl = document.getElementById("msg-cadastro-vendedor");
      msgEl.textContent = 'Processando...';
      
      const senha = formData.get('senha');
      const confirmaSenha = formData.get('confirmar_senha');
      
      if (senha !== confirmaSenha) {
        msgEl.style.color = 'red';
        msgEl.textContent = 'Erro: As senhas não coincidem!';
        return;
      }
      
      formData.append("acao", "cadastrar");
      
      try {
        const response = await fetch("/AV2DAW/views/adm/cadastrar_admin.php", {
          method: "POST",
          body: formData,
        });
        
        const result = await response.json();
        
        if (result.success) {
          msgEl.style.color = 'green';
          msgEl.textContent = "Vendedor cadastrado com sucesso! Use a senha inicial.";
          
          setTimeout(() => {
            fecharModalCadastroVendedor();
            document.dispatchEvent(new Event("atualizarDashboard"));
          }, 1500);
        } else {
          msgEl.style.color = 'red';
          msgEl.textContent = "Erro: " + (result.error || result.message || "Erro desconhecido.");
        }
      } catch (err) {
        console.error("Erro ao cadastrar vendedor:", err);
        msgEl.style.color = 'red';
        msgEl.textContent = "Erro de comunicação com o servidor.";
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
    // Determine se a locação está PRÉ (Reservado) ou PÓS (Devolvido)
    // Se o status é Reservado, a ação é iniciar a Retirada (Check-out)
    // Se o status é PÓS/Devolvido, a ação é a Vistoria de Devolução (Check-in)
    const acaoTexto = checklist.tipo === 'PRÉ' ? 'Abrir Check-out' : 'Abrir Check-in';
    
    // O seu Check-in/Vistoria lida com o ID da locação.
    const idLocacao = checklist.id_locacao;
    
    const row = `
      <tr>
        <td>${checklist.doc_cliente}</td>
        <td>${checklist.modelo}</td>
        <td>${checklist.data}</td>
        <td>${checklist.tipo}</td>
        <td>
          <button class="btn btn-success" onclick="abrirCheckin(${idLocacao})">
            ${acaoTexto}
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

// ============ FUNÇÕES DE CHECK-IN E CHECK-OUT ============
function abrirCheckin(idLocacao) {
  if (!idLocacao) {
    alert('ID da locação inválido.');
    return;
  }
  // Redireciona para a página de check-in
  window.location.href = `../adm/checkin.php?id_locacao=${encodeURIComponent(idLocacao)}`;
}

function abrirCheckout(idLocacao) {
  // Lógica para abrir o check-out
  console.log("Abrir check-out para locação ID:", idLocacao);
  // Aqui você pode carregar os dados da locação e abrir um modal ou redirecionar para outra página
}

// ============ FUNÇÕES DE AÇÃO EM MASSA ============
function acaoEmMassa(acao) {
  const checkboxes = document.querySelectorAll('input[name="veiculoSelecionado"]:checked');
  const idsSelecionados = Array.from(checkboxes).map((cb) => cb.value);

  if (idsSelecionados.length === 0) {
    return alert("Nenhum veículo selecionado!");
  }

  if (acao === "excluir") {
    if (confirm("Tem certeza que deseja excluir os veículos selecionados?")) {
      // Chamar a função de exclusão em massa
      excluirEmMassa(idsSelecionados);
    }
  } else if (acao === "editar") {
    if (idsSelecionados.length === 1) {
      // Se apenas um veículo for selecionado, abrir o modal de edição
      const veiculoSelecionado = idsSelecionados[0];
      abrirModalEdicao(veiculoSelecionado);
    } else {
      alert("Selecione apenas um veículo para editar.");
    }
  } else {
    alert("Ação desconhecida.");
  }
}

function excluirEmMassa(ids) {
  const formData = new FormData();
  formData.append("acao", "deletarEmMassa");
  ids.forEach((id) => formData.append("ids[]", id));

  fetch("/AV2DAW/views/adm/remover_veiculo.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Veículos excluídos com sucesso!");
        document.dispatchEvent(new Event("atualizarDashboard"));
      } else {
        alert("Erro: " + result.error);
      }
    })
    .catch((err) => {
      console.error("Erro ao excluir em massa:", err);
      alert("Erro ao excluir veículos!");
    });
}

// ============ FUNÇÕES DE FILTRO E BUSCA ============
function aplicarFiltros() {
  const modelo = document.getElementById("filtroModelo").value;
  const marca = document.getElementById("filtroMarca").value;
  const ano = document.getElementById("filtroAno").value;
  const categoria = document.getElementById("filtroCategoria").value;
  const cor = document.getElementById("filtroCor").value;
  const placa = document.getElementById("filtroPlaca").value;
  const disponivel = document.getElementById("filtroDisponivel").value;

  const filtros = {
    modelo,
    marca,
    ano,
    categoria,
    cor,
    placa,
    disponivel: disponivel === "1" ? true : disponivel === "0" ? false : null,
  };

  // Atualizar a URL com os parâmetros de filtro
  const url = new URL(window.location);
  Object.keys(filtros).forEach((key) => url.searchParams.set(key, filtros[key]));
  window.history.pushState({}, "", url);

  // Recarregar o dashboard com os novos filtros
  carregarDashboard();
}

function limparFiltros() {
  document.getElementById("filtroModelo").value = "";
  document.getElementById("filtroMarca").value = "";
  document.getElementById("filtroAno").value = "";
  document.getElementById("filtroCategoria").value = "";
  document.getElementById("filtroCor").value = "";
  document.getElementById("filtroPlaca").value = "";
  document.getElementById("filtroDisponivel").value = "";

  // Remover parâmetros de filtro da URL
  const url = new URL(window.location);
  Object.keys(filtros).forEach((key) => url.searchParams.delete(key));
  window.history.pushState({}, "", url);

  // Recarregar o dashboard sem filtros
  carregarDashboard();
}

// ============ FUNÇÕES DE AJUDA E SUPORTE ============
function abrirAjuda() {
  const modal = document.getElementById("modalAjuda");
  if (!modal) {
    console.error("Modal de ajuda não encontrado!");
    return;
  }
  modal.style.display = "flex";
}

function fecharAjuda() {
  const modal = document.getElementById("modalAjuda");
  if (!modal) {
    console.error("Modal de ajuda não encontrado!");
    return;
  }
  modal.style.display = "none";
}

// ============ EVENT LISTENERS GLOBAIS ============
window.addEventListener("click", function (e) {
  const modals = ["modalCadastro", "modalEdicao", "modalAjuda"];
  modals.forEach((modalId) => {
    const modal = document.getElementById(modalId);
    if (modal && e.target === modal) {
      modal.style.display = "none";
    }
  });
});

window.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    fecharModalCadastro();
    fecharModalEdicao();
    fecharAjuda();
  }
});

// ============ INICIALIZAÇÃO ============
document.addEventListener("DOMContentLoaded", function () {
  // Inicializar tooltips
  const tooltips = document.querySelectorAll("[data-tooltip]");
  tooltips.forEach((tooltip) => {
    new Tooltip(tooltip);
  });

  // Carregar dados iniciais
  carregarDashboard();
});
