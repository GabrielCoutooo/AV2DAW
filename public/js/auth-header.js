document.addEventListener("DOMContentLoaded", async () => {
  const userAuthContainer = document.getElementById("user-auth-container");

  if (!userAuthContainer) {
    console.error("Elemento #user-auth-container não encontrado no HTML.");
    return;
  }

  try {
    const response = await fetch("../../public/check-status.php");
    const data = await response.json();

    userAuthContainer.innerHTML = "";

    if (data.loggedIn) {
      // Se o usuário estiver logado, cria a saudação e o botão de sair
      userAuthContainer.innerHTML = `
                <span class="nav-link" style="color: #00bfff;">Olá, ${data.user.nome} !</span>
                <a href="/AV2DAW/public/logout.php" class="nav-link btn-login" style="margin-left: 1rem;">Sair</a>
            `;
    } else {
      // Se não estiver logado, cria os botões de login e cadastro
      userAuthContainer.innerHTML = `
                <button class="btn-login" onclick="window.location.href='login.html'">
                    <i class="fa-regular fa-circle-user"></i>
                    Login
                </button>
            `;
    }
  } catch (error) {
    console.error("Erro ao verificar status de autenticação:", error);
    // Em caso de erro, mostra o botão de login como padrão
    userAuthContainer.innerHTML = `<button class="btn-login" onclick="window.location.href='login.html'">Login</button>`;
  }
});
