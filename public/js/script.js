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
});
