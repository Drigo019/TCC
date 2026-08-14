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

function abrirZoom() {

    const imagem = document.getElementById("previewImagem");
    const imagemZoom = document.getElementById("imagemZoom");
    const modal = document.getElementById("zoomModal");

    imagemZoom.src = imagem.src;

    // Remove qualquer estado anterior
    modal.classList.remove("fechando");

    // Mostra o modal
    modal.classList.add("ativo");
}


function fecharZoom() {

    const modal = document.getElementById("zoomModal");

    // Começa a animação de fechamento
    modal.classList.remove("ativo");
    modal.classList.add("fechando");

    // Espera a animação terminar
    setTimeout(() => {

        modal.classList.remove("fechando");

    }, 200);
}
function mudarCategoria(categoria) {

    const body = document.body;

    // Remove a categoria anterior
    body.classList.remove(
        "categoria-frio",
        "categoria-defumado",
        "categoria-doce",
        "categoria-bebida",
        "categoria-queijo"
    );

    // Aplica a nova categoria
    switch (categoria) {

        case "Frio":
            body.classList.add("categoria-frio");
            break;

        case "Defumado":
            body.classList.add("categoria-defumado");
            break;

        case "Doce":
            body.classList.add("categoria-doce");
            break;

        case "Bebida":
            body.classList.add("categoria-bebida");
            break;

        case "Queijo":
            body.classList.add("categoria-queijo");
            break;
    }
}
function buscarProduto() {

    const codigo = document.getElementById("codigo").value.trim();

    if (codigo === "") {
        alert("Digite um código de barras.");
        return;
    }

    fetch("buscar_produto.php?codigo=" + encodeURIComponent(codigo))
        .then(response => response.json())
        .then(produto => {

            if (produto.erro) {
                alert(produto.erro);
                return;
            }

            // Aqui você adiciona o produto à venda
            adicionarProduto(produto);

            // Limpa o campo para o próximo produto
            document.getElementById("codigo").value = "";
            document.getElementById("codigo").focus();

        })
        .catch(erro => {
            console.error(erro);
            alert("Erro ao buscar o produto.");
        });
}
document.getElementById("codigo").addEventListener("keydown", function(event) {

    if (event.key === "Enter") {
        event.preventDefault();
        buscarProduto();
    }

});