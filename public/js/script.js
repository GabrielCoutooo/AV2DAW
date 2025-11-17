/**
 * Script global da aplicação
 */

document.addEventListener("DOMContentLoaded", function () {
  console.log("Aplicação carregada com sucesso");

  // Fechar página de login
  const fecharLogin = document.querySelector(".fechar-login");
  if (fecharLogin) {
    fecharLogin.addEventListener("click", function () {
      window.history.back();
    });
  }
  carregarVeiculosHomePage();
  inicializarFiltroDisponibilidade();
});
async function carregarVeiculosHomePage() {
  try {
    const response = await fetch("../../public/api/client/listar-veiculos.php");
    if (!response.ok) {
      throw new Error("Erro ao carregar veículos: " + response.statusText);
    }
    const data = await response.json();
    if (data.error) {
      console.error("Erro na resposta da API:", data.error);
      document.getElementById("populares-grid").innerHTML =
        "<p>Faça Login para ver os veículos populares.</p>";
      document.getElementById("recomendados-grid").innerHTML =
        "<p>Faça Login para ver os veículos recomendados.</p>";
      return;
    }
    const todos = [
      ...(data.populares || []),
      ...(data.recomendados || []),
      ...(data.suv || []),
      ...(data.outros || []),
    ];
    renderizarCarros(data.populares, "populares-grid");
    renderizarCarros(data.recomendados, "recomendados-grid");
    renderizarCarros(todos, "todos-grid");
  } catch (error) {
    console.error("Erro ao carregar veículos:", error);
    document.getElementById("populares-grid").innerHTML =
      "<p>Erro ao carregar veículos populares.</p>";
    document.getElementById("recomendados-grid").innerHTML =
      "<p>Erro ao carregar veículos recomendados.</p>";
  }
}

// === Navegação de carrossel ===
function configurarCarrossel(botaoPrevId, botaoNextId, gridId) {
  const btnPrev = document.getElementById(botaoPrevId);
  const btnNext = document.getElementById(botaoNextId);
  const grid = document.getElementById(gridId);

  if (!btnPrev || !btnNext || !grid) {
    console.warn("Elementos do carrossel não encontrados:", {
      botaoPrevId,
      botaoNextId,
      gridId,
    });
    return;
  }

  const passo = 300; // pixels por clique

  btnPrev.addEventListener("click", () => {
    grid.scrollBy({ left: -passo, behavior: "smooth" });
  });

  btnNext.addEventListener("click", () => {
    grid.scrollBy({ left: passo, behavior: "smooth" });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  configurarCarrossel("pop-prev", "pop-next", "populares-grid");
  configurarCarrossel("rec-prev", "rec-next", "recomendados-grid");
  configurarCarrossel("all-prev", "all-next", "todos-grid");
});

/**
 * Renderiza os cards dos carros em um container específico.
 * @param {Array} carros - Array de objetos de carros vindo da API.
 * @param {string} containerId - O ID do elemento grid (ex: 'populares-grid').
 */
function renderizarCarros(carros, containerId) {
  const container = document.getElementById(containerId);
  if (!container) {
    console.error("Container não encontrado:", containerId);
    return;
  }

  container.innerHTML = "";

  if (!Array.isArray(carros) || carros.length === 0) {
    container.innerHTML = "<p>Nenhum carro disponível nesta categoria.</p>";
    return;
  }

  carros.forEach((carro) => {
    const card = document.createElement("div");
    card.className = "carro-card";

    const precoFormatado = parseFloat(carro.preco_diaria_base).toLocaleString(
      "pt-BR",
      {
        style: "currency",
        currency: "BRL",
      }
    );

    // Exibe todas as categorias do veículo
    const categorias = carro.categorias || carro.categoria || "";
    const categoriasDisplay = categorias ? categorias : "Sem categoria";

    card.innerHTML = `
            <div class="card-header">
                <h3>${carro.nome_modelo}</h3>
                <button class="btn-like">♡</button> </div>
            <p class="carro-categoria">${categoriasDisplay}</p>
            
            <div class="card-imagem">
                <img src="/AV2DAW/public/images/uploads/carros/${
                  carro.imagem ? carro.imagem : "default.png"
                }" alt="${carro.nome_modelo}">
                            </div>
            
        <div class="card-specs">
        <span><i class="fas fa-gas-pump"></i> 90L</span> <span><i class="fas fa-cogs"></i> ${
          carro.tipo_transmissao
        }</span>
        <span><i class="fas fa-user-friends"></i> ${
          carro.capacidade_pessoas
        } Pessoas</span>
    </div>
    <div class="card-footer">
        <div class="carro-preco">
            <strong>${precoFormatado}</strong>/dia
            <s>R$100,00</s> </div>
        <button class="btn-alugar">Alugue</button>
    </div>
`;

    container.appendChild(card);

    // Adicionar funcionalidade de favorito ao coração
    const btnLike = card.querySelector(".btn-like");
    if (btnLike) {
      // Verificar se o veículo está favoritado no localStorage
      const favoritosArmazenados = JSON.parse(
        localStorage.getItem("veiculosFavoritos") || "[]"
      );
      const idVeiculo = String(carro.id_veiculo || carro.id || "");

      if (favoritosArmazenados.includes(idVeiculo)) {
        btnLike.classList.add("liked");
        btnLike.innerHTML = "♥";
      }

      btnLike.addEventListener("click", (e) => {
        e.stopPropagation();
        btnLike.classList.toggle("liked");

        if (btnLike.classList.contains("liked")) {
          btnLike.innerHTML = "♥";
          // Adicionar aos favoritos
          if (!favoritosArmazenados.includes(idVeiculo)) {
            favoritosArmazenados.push(idVeiculo);
            localStorage.setItem(
              "veiculosFavoritos",
              JSON.stringify(favoritosArmazenados)
            );
          }
        } else {
          btnLike.innerHTML = "♡";
          // Remover dos favoritos
          const index = favoritosArmazenados.indexOf(idVeiculo);
          if (index > -1) {
            favoritosArmazenados.splice(index, 1);
            localStorage.setItem(
              "veiculosFavoritos",
              JSON.stringify(favoritosArmazenados)
            );
          }
        }
      });
    }

    // Navegar para tela de checkout com o veículo selecionado
    const btnAlugar = card.querySelector(".btn-alugar");
    if (btnAlugar) {
      btnAlugar.addEventListener("click", () => {
        const id = carro.id_veiculo || carro.id || "";
        if (id) {
          // redireciona para checkout.html
          window.location.href = `/AV2DAW/views/client/checkout.html?id=${encodeURIComponent(
            id
          )}`;
        }
      });
    }
  });
}

// === Filtro de disponibilidade ===
let cacheTodosVeiculos = [];
async function inicializarFiltroDisponibilidade() {
  const form = document.getElementById("form-busca-veiculos");
  const selModelos = document.getElementById("modelos");
  const inputPesquisa = document.getElementById("pesquisa");
  const inputData = document.getElementById("data");
  const inputHora = document.getElementById("horario");
  if (!form) return;

  // Carrega base inicial para popular select de modelos
  try {
    const resp = await fetch("../../public/api/client/listar-veiculos.php");
    const json = await resp.json();
    if (!resp.ok || json.error) return;
    cacheTodosVeiculos = [
      ...(json.populares || []),
      ...(json.recomendados || []),
      ...(json.suv || []),
      ...(json.outros || []),
    ];
    const modelosUnicos = [];
    const seen = new Set();
    cacheTodosVeiculos.forEach((v) => {
      if (!seen.has(v.id_modelo)) {
        seen.add(v.id_modelo);
        modelosUnicos.push(v);
      }
    });
    modelosUnicos.sort((a, b) => a.nome_modelo.localeCompare(b.nome_modelo));
    modelosUnicos.forEach((m) => {
      const opt = document.createElement("option");
      opt.value = m.id_modelo;
      opt.textContent = m.nome_modelo;
      selModelos.appendChild(opt);
    });
  } catch (e) {
    console.warn("Falha ao popular modelos", e);
  }

  function montarQueryParams() {
    const tipo =
      form.querySelector('input[name="tipo"]:checked')?.value || "retirada";
    const data = inputData.value;
    const hora = inputHora.value;
    const modeloId = selModelos.value;
    // Campo de pesquisa pode não existir em algumas páginas
    const q = inputPesquisa ? inputPesquisa.value.trim() : "";
    const params = new URLSearchParams();
    params.set("tipo", tipo);
    if (data) params.set("data", data);
    if (hora) params.set("hora", hora);
    if (modeloId) params.set("modelo_id", modeloId);
    if (q) params.set("q", q);
    return params.toString();
  }

  async function consultarDisponibilidade(e) {
    if (e) e.preventDefault();
    const qs = montarQueryParams();
    try {
      const resp = await fetch(
        `../../public/api/client/disponibilidade.php?${qs}`
      );
      const data = await resp.json();
      if (!resp.ok || data.error) {
        console.warn("Erro disponibilidade", data.error);
        return;
      }
      // Renderiza resultado em grid "todos"
      renderizarCarros(data.veiculos, "todos-grid");
    } catch (err) {
      console.error(err);
    }
  }

  // Submeter form
  form.addEventListener("submit", consultarDisponibilidade);
  // Filtros reativos
  [selModelos, inputPesquisa, inputData, inputHora].forEach((el) => {
    if (el) el.addEventListener("change", consultarDisponibilidade);
    if (el && el === inputPesquisa)
      el.addEventListener("input", () => {
        // debounce simples
        clearTimeout(inputPesquisa._t);
        inputPesquisa._t = setTimeout(consultarDisponibilidade, 300);
      });
  });
}
