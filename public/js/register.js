document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form-cadastro");
  const msg = document.getElementById("mensagem-cadastro");

  form.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    try {
      const response = await fetch("../../public/register.php", {
        method: "POST",
        body: formData,
      });
      const data = await response.json();
      msg.textContent = data.message;
      msg.style.color = data.success ? "green" : "red";
      if (data.success) {
        setTimeout(
          () => (window.location.href = "../../views/client/login.html"),
          2000
        );
      }
    } catch (error) {
      msg.textContent = "Erro ao processar o cadastro. Tente novamente.";
      msg.style.color = "red";
    }
  });
});
