(function () {
  // Seleciona todos os itens FAQ
  const itensFaq = document.querySelectorAll(".faq-item");

  itensFaq.forEach((item) => {
    const botaoPergunta = item.querySelector(".faq-question");
    const resposta = item.querySelector(".faq-answer");
    const icone = item.querySelector(".faq-icon");

    botaoPergunta.addEventListener("click", () => {
      // Fecha outros itens abertos
      itensFaq.forEach((outroItem) => {
        if (outroItem !== item && outroItem.classList.contains("active")) {
          outroItem.classList.remove("active");
          outroItem.querySelector(".faq-answer").style.maxHeight = null;
          outroItem.querySelector(".faq-icon").style.transform = "rotate(0deg)";
        }
      });

      // Toggle do item atual
      item.classList.toggle("active");

      if (item.classList.contains("active")) {
        resposta.style.maxHeight = resposta.scrollHeight + "px";
        icone.style.transform = "rotate(180deg)";
      } else {
        resposta.style.maxHeight = null;
        icone.style.transform = "rotate(0deg)";
      }
    });
  });
})();
