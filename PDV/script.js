const select = document.getElementById("armazenamento");
const form = document.getElementById("formProduto");
const botoes = document.querySelectorAll(".btnSalvar");

select.addEventListener("change", function () {

    // Remove os temas anteriores
    form.classList.remove("tema-azul", "tema-amarelo");

    if (this.value === "Refrigerado") {

        // Aplica o tema azul
        form.classList.add("tema-azul");

        // Botão azul
        botoes.forEach(botao => {
            botao.classList.remove("btn-warning", "btn-success");
            botao.classList.add("btn-primary");
        });

    } else if (this.value === "Normal") {

        // Aplica o tema amarelo
        form.classList.add("tema-amarelo");

        // Botão amarelo
        botoes.forEach(botao => {
            botao.classList.remove("btn-primary", "btn-success");
            botao.classList.add("btn-warning");
        });

    } else {

        // Caso volte para "Selecione..."
        botoes.forEach(botao => {
            botao.classList.remove("btn-primary", "btn-warning");
            botao.classList.add("btn-success");
        });

    }

    

}); 

const inputImagem = document.getElementById("arquivo");
const preview = document.getElementById("previewImagem");

inputImagem.addEventListener("change", function () {

    const arquivo = this.files[0];

    if (arquivo) {
        const leitor = new FileReader();

        leitor.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = "block";
        };

        leitor.readAsDataURL(arquivo);
    } else {
        preview.src = "";
        preview.style.display = "none";
    }
});

