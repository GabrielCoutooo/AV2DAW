/**
 * Script global da aplicação
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Aplicação carregada com sucesso');

    // Controlar Modal de Login
    const btnLoginModal = document.getElementById('btn-login-modal');
    const modalLogin = document.getElementById('modal-login');
    const fecharModal = document.getElementById('fechar-modal');
    
    // Abrir modal
    btnLoginModal.addEventListener('click', function() {
        modalLogin.classList.add('ativo');
        document.body.classList.add('modal-aberto');
    });
    
    // Fechar modal
    fecharModal.addEventListener('click', function() {
        modalLogin.classList.remove('ativo');
        document.body.classList.remove('modal-aberto');
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(event) {
        if (event.target === modalLogin) {
            modalLogin.classList.remove('ativo');
            }
    });
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
