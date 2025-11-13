/**
 * login.js - Script de controle para página de login
 *
 * Funcionalidades:
 * 1. Toggle de visibilidade de senha
 * 2. Efeitos de hover nos botões das redes sociais
 * 3. Validação básica de formulário
 */
//Aguardando o carregamento completo do DOM
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-login");
  const emailInput = document.getElementById("email-login");
  const passwordInput = document.getElementById("senha-login");
  const togglePassword = document.getElementById("toggle-senha");
  const rememberMeCheckbox = document.getElementById("lembrar-login");
  const mensagem = document.getElementById("mensagem-login");
  /**
   * 1. TOGGLE DE VISIBILIDADE DE SENHA
   * Permite que o usuário visualize ou oculte a senha ao clicar no ícone de olho.
   */
  // Verificando se os elementos existem antes de adicionar event listeners
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      //Alternando entre o tipo password e texto
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      //Alternando o ícone entre olho aberto e fechado
      this.querySelector("i").classList.toggle("fa-eye");
      this.querySelector("i").classList.toggle("fa-eye-slash");
    });
  }
  if (localStorage.getItem("emailSalvo")) {
    emailInput.value = localStorage.getItem("emailSalvo");
    rememberMeCheckbox.checked = true;
  }
  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    mensagem.textContent = "Verificando...";
    mensagem.style.color = "gray";

    const dados = new FormData(form);
    try {
      const resposta = await fetch("../public/login.php", {
        method: "POST",
        body: dados,
      });
      const resultado = await resposta.json();
      if (resultado.success) {
        mensagem.textContent = "Login realizado com sucesso! Redirecionando...";
        mensagem.style.color = "green";
        if (rememberMeCheckbox.checked) {
          localStorage.setItem("emailSalvo", emailInput.value);
        } else {
          localStorage.removeItem("emailSalvo");
        }
        setTimeout(() => {
          window.location.href = resultado.redirect;
        }, 1000);
      } else {
        mensagem.textContent =
          resultado.message || "Erro no login. Tente novamente.";
        mensagem.style.color = "red";
      }
    } catch (error) {
      mensagem.textContent = "Erro ao processar o login. Tente novamente.";
      mensagem.style.color = "red";
    }
  });

  /**
   * 3. VALIDAÇÃO BÁSICA DE FORMULÁRIO
   * Verifica se os campos obrigatórios estão preenchidos antes do envio.
   */
  const loginForm = document.querySelector(".login-form");
  if (loginForm) {
    /**
     * Evento de envio do formulário
     * Valida os campos antes do envio para o servidor
     */
    loginForm.addEventListener("submit", function (e) {
      const email = document.getElementById("email");
      const senha = document.getElementById("senha");

      /**
       * Validação de campos obrigatórios
       * Verifica se os campos de email e senha estão preenchidos
       */
      if (!email.value || !senha.value) {
        // Impede o envio do formulario caso vazio
        e.preventDefault();
        // Adiciona classes de erro para feedback visual
        if (!email.value) {
          // Foca no campo de email
          email.focus();
        } else {
          // Foca no campo da senha
          senha.focus();
        }
      }
    });
  }
});
// Verificando se o JavaScript foi carregado
console.log("JavaScript Carregado!(login.js)");
