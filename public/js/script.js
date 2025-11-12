/**
 * Script global da aplicação
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Aplicação carregada com sucesso');
});
// Fechar página de login
const fecharLogin = document.querySelector('.fechar-login');
fecharLogin.addEventListener('click', function() {
    window.history.back();
});

// Prevenir envio do formulário (exemplo)
const formLogin = document.getElementById('form-login');
formLogin.addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Formulário de login enviado!');
});
// Controlar Modal de Login
const btnLoginModal = document.getElementById('btn-login-modal');
const modalLogin = document.getElementById('modal-login');
const fecharModal = document.getElementById('fechar-modal');

// Abrir modal
btnLoginModal.addEventListener('click', function() {
    modalLogin.classList.add('ativo');
});

// Fechar modal
fecharModal.addEventListener('click', function() {
    modalLogin.classList.remove('ativo');
});

// Fechar modal ao clicar fora
window.addEventListener('click', function(event) {
    if (event.target === modalLogin) {
        modalLogin.classList.remove('ativo');
        }
});
