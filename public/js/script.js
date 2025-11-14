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
});
async function carregarVeiculosHomePage() {
  try {
    const response = await fetch("../../public/api/client/listar-veiculos.php");
    if (!response.ok) {
      throw new Error("Erro ao carregar veículos: " + response.statusText);
    }
    const data = await response.json();
    renderizarCarros(data.populares, "populares-grid");
  } catch (error) {
    console.error("Erro ao carregar veículos:", error);
    document.getElementById("populares-grid").innerHTML =
      "<p>Erro ao carregar veículos populares.</p>";
    document.getElementById("recomendados-grid").innerHTML =
      "<p>Erro ao carregar veículos recomendados.</p>";
  }
}
/**
 * Renderiza os cards dos carros em um container específico.
 * @param {Array} carros - Array de objetos de carros vindo da API.
 * @param {string} containerId - O ID do elemento grid (ex: 'populares-grid').
 */
function renderizarCarros(carros, containerId) {
  const container = document.getElementById(containerId);

  container.innerHTML = "";

  if (carros.length === 0) {
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

    card.innerHTML = `
            <div class="card-header">
                <h3>${carro.nome_modelo}</h3>
                <button class="btn-like">♡</button> </div>
            <p class="carro-categoria">${carro.categoria}</p>
            
            <div class="card-imagem">
                            </div>
            
        <div class="card-specs">
        <span><i class="fas fa-gas-pump"></i> 90L</span> <span><i class="fas fa-cogs"></i> ${carro.tipo_transmissao}</span>
        <span><i class="fas fa-user-friends"></i> ${carro.capacidade_pessoas} Pessoas</span>
    </div>
    <div class="card-footer">
        <div class="carro-preco">
            <strong>${precoFormatado}</strong>/semana
            <s>R$100,00</s> </div>
        <button class="btn-alugar">Alugue</button>
    </div>
`;

    container.appendChild(card);
  });
}
