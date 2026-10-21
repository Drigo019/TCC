<?php

include 'conexao.php';

$sql = "
    SELECT
        idProduto,
        nome,
        codigoDeBarras,
        categoria,
        estoque
    FROM produtos
    ORDER BY codigodebarras ASC
";

$resultado = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title>Estoque - PDV</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="css/estilo.css">

<style>

/* =========================================
   FONTE
========================================= */

@font-face {
    font-family: RopaSans;
    src: url(fontes/RopaSans-Regular.ttf);
}


/* =========================================
   CORPO
========================================= */

body {
    margin: 0;
    background: #f4f6fb;
    font-family: 'Segoe UI', sans-serif;
}


/* =========================================
   SIDEBAR
========================================= */

.sidebar {

    width: 240px;
    height: 100vh;

    position: fixed;

    background: linear-gradient(
        black
    );

    padding: 30px 20px;

    border-radius: 0 20px 20px 0;

    box-shadow: 5px 0 20px rgba(0,0,0,0.1);
}

/* =========================================
   SIDEBAR
========================================= */

.sidebar {

width: 240px;

height: 100vh;

position: fixed;

left: 0;

top: 0;

background: black;

padding: 30px 20px;

border-radius: 0 20px 20px 0;

box-shadow: 5px 0 20px rgba(0,0,0,0.1);

z-index: 1000;

transition: transform 0.3s ease;

}


/* =========================================
CONTEÚDO
========================================= */

.conteudo-principal {

margin-left: 260px;

transition: margin-left 0.3s ease;

}


/* =========================================
BOTÃO DA SIDEBAR
========================================= */

.botao-sidebar {

position: absolute;

top: 20px;

right: -18px;

width: 36px;

height: 36px;

border: none;

border-radius: 50%;

background: #212529;

color: white;

display: flex;

align-items: center;

justify-content: center;

cursor: pointer;

box-shadow: 0 4px 12px rgba(0,0,0,0.3);

transition: 0.3s;

z-index: 1001;

}


.botao-sidebar:hover {

transform: scale(1.1);

background: #343a40;

}


/* =========================================
SIDEBAR FECHADA
========================================= */

.sidebar.escondida {

transform: translateX(-100%);

}


/* =========================================
CONTEÚDO SEM SIDEBAR
========================================= */

.content.sidebar-fechada {
    margin-left: 20px;
}


/* =========================================
BOTÃO QUANDO FECHADA
========================================= */

.sidebar.escondida .botao-sidebar {

right: -54px;

}


.sidebar.escondida .botao-sidebar i {

transform: rotate(180deg);

}


/* LOGO */

.logo {

    color: white;

    font-weight: bold;

    margin-bottom: 40px;

    font-size: 20px;

    font-family: 'RopaSans';
}


/* LINKS */

.sidebar a {

    display: flex;

    align-items: center;

    gap: 12px;

    color: #d1d5db;

    padding: 14px 16px;

    text-decoration: none;

    border-radius: 14px;

    margin-bottom: 10px;

    transition: 0.3s;

    font-size: 16px;
}

/* =========================================
   ABA ATIVA
========================================= */

.sidebar a.ativo {
    background: #374151;
    color: white;

    transform: scale(1.08);

    position: relative;
    z-index: 10;

    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);

    margin-left: 3px;
    margin-right: -3px;
}


/* HOVER DOS LINKS */

.sidebar a:hover {

    background: #374151;

    color: white;

    transform: translateX(5px);
}


/* ÍCONES */

.sidebar i {

    font-size: 20px;
}


/* =========================================
   CONTEÚDO
========================================= */

.content {

    margin-left: 260px;

    padding: 35px;
}


/* =========================================
   TÍTULO
========================================= */

.titulo {

    font-size: 38px;

    font-weight: bold;

    color: #111827;

    margin-bottom: 5px;
}


.subtitulo {

    color: #6b7280;

    margin-bottom: 35px;
}


/* =========================================
   PAINEL
========================================= */

.painel {

    background: white;

    border-radius: 22px;

    padding: 25px;

    margin-top: 35px;

    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}


.painel h4 {

    margin-bottom: 20px;

    font-weight: bold;
}


/* =========================================
   CAMPO DE BUSCA
========================================= */

#busca {

    height: 48px;

    border-radius: 10px;

    border: 1px solid #d1d5db;

    padding: 10px 15px;

    font-size: 15px;

    transition: 0.2s;
}


#busca:focus {

    border-color: #1c4de0;

    box-shadow: 0 0 0 3px rgba(28,77,224,0.15);

    outline: none;
}


/* =========================================
   TABELA
========================================= */

table {

    margin-top: 15px;
}


.table {

    vertical-align: middle;
}


.table thead th {

    color: #111827;

    font-weight: 600;

    border-bottom: 2px solid #e5e7eb;

    white-space: nowrap;
}


.table tbody td {

    color: #374151;

    border-bottom: 1px solid #e5e7eb;

    padding-top: 12px;

    padding-bottom: 12px;
}


.table-hover tbody tr:hover {

    background-color: #f8fafc;
}


/* =========================================
   ESTOQUE
========================================= */

.estoque-normal {

    color: #198754;

    font-weight: bold;
}


.estoque-baixo {

    color: #dc3545;

    font-weight: bold;
}


/* =========================================
   CAMPO DE QUANTIDADE
========================================= */

.quantidade-adicionar {

    width: 115px;

    border-radius: 8px;

    border: 1px solid #d1d5db;

    padding: 8px 10px;
}


.quantidade-adicionar:focus {

    border-color: #198754;

    box-shadow: 0 0 0 3px rgba(25,135,84,0.15);

    outline: none;
}


/* =========================================
   BOTÃO ADICIONAR
========================================= */

.btn-success {

    border-radius: 8px;

    padding: 8px 14px;

    font-weight: 500;

    transition: 0.2s;
}


.btn-success:hover {

    transform: translateY(-1px);

    box-shadow: 0 4px 10px rgba(25,135,84,0.2);
}


/* =========================================
   RESPONSIVIDADE
========================================= */

@media (max-width: 900px) {

    .sidebar {

        width: 200px;
    }

    .content {

        margin-left: 220px;

        padding: 25px;
    }
}


@media (max-width: 700px) {

    .sidebar {

        width: 100%;

        height: auto;

        position: relative;

        border-radius: 0;

        padding: 15px;
    }


    .logo {

        margin-bottom: 15px;
    }


    .sidebar a {

        display: inline-flex;

        margin-right: 5px;

        margin-bottom: 5px;
    }


    .content {

        margin-left: 0;

        padding: 20px;
    }

}


/* =========================================
   POP-UPS PERSONALIZADOS DO ESTOQUE
========================================= */

.notificacao-overlay {
    position: fixed;
    inset: 0;
    z-index: 3000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(12, 16, 20, 0.65);
    backdrop-filter: blur(5px);
}

.notificacao-overlay.visivel {
    display: flex;
    animation: aparecerOverlayEstoque 0.2s ease;
}

.notificacao-card {
    width: 100%;
    max-width: 420px;
    padding: 32px 28px;
    border-radius: 24px;
    background: #fff;
    text-align: center;
    box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
    animation: subirNotificacaoEstoque 0.3s ease;
}

.notificacao-icone {
    width: 78px;
    height: 78px;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 38px;
}

.notificacao-icone.sucesso {
    color: #198754;
    background: #d9f8e6;
}

.notificacao-icone.erro {
    color: #dc3545;
    background: #ffe1e4;
}

.notificacao-icone.confirmacao {
    color: #b8860b;
    background: #fff1c2;
}

.notificacao-icone.info {
    color: #0d6efd;
    background: #dceaff;
}

.notificacao-titulo {
    margin-bottom: 12px;
    color: #212529;
    font-size: 24px;
    font-weight: 700;
}

.notificacao-mensagem {
    margin-bottom: 26px;
    color: #6c757d;
    font-size: 15px;
    line-height: 1.7;
    white-space: pre-line;
    overflow-wrap: anywhere;
}

.notificacao-acoes {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.notificacao-botao {
    min-height: 46px;
    padding: 11px 22px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    transition: transform 0.2s, filter 0.2s;
}

.notificacao-botao:hover {
    transform: translateY(-2px);
    filter: brightness(0.96);
}

.notificacao-botao.principal {
    color: #29230d;
    background: #f5c542;
}

.notificacao-botao.secundario {
    color: #495057;
    background: #e9ecef;
}

.notificacao-botao:disabled {
    opacity: 0.65;
    cursor: wait;
    transform: none;
}

@keyframes aparecerOverlayEstoque {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes subirNotificacaoEstoque {
    from {
        opacity: 0;
        transform: translateY(15px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 480px) {
    .notificacao-card {
        padding: 26px 20px;
    }

    .notificacao-acoes {
        flex-direction: column-reverse;
    }

    .notificacao-botao {
        width: 100%;
    }
}


</style>

</head>


<body>


<!-- MENU -->

<div class="sidebar">

    <h2>
        <img id="logo" src="../imagens/logo.jpeg" style="height: 100px; width: 180px; margin-left: 0px; margin-right: 0px; margin-top: -20px;">
        <button
    type="button"
    class="botao-sidebar"
    onclick="alternarSidebar()"
    title="Ocultar menu"
>
    <i class="bi bi-chevron-left"></i>
</button>
    </h2>

    <a href="index.php">
        <i class="bi bi-house"></i>
        Dashboard
    </a>

    <a href="pdv.html">
        <i class="bi bi-cart"></i>
        PDV
    </a>

    <a href="produtos.html">
        <i class="bi bi-box-seam"></i>
        Produtos
    </a>

    <a href="estoque.php" class='ativo'>
        <i class="bi bi-boxes"></i>
        Estoque
    </a>

</div>


<!-- CONTEÚDO -->

<div class="content">

    <h2>Controle de Estoque</h2>

    <p class="text-muted">
        Consulte e aumente a quantidade dos produtos cadastrados.
    </p>


    <div class="card p-4">


        <!-- BUSCA -->

        <div class="mb-4">

            <input
                type="text"
                id="busca"
                class="form-control"
                placeholder="🔍 Buscar produto por nome ou código..."
                onkeyup="filtrarProdutos()"
            >

        </div>


        <!-- TABELA -->

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>Código</th>

                        <th>Produto</th>

                        <th>Categoria</th>

                        <th>Estoque atual</th>

                        <th>Quantidade</th>

                        <th>Ação</th>

                    </tr>

                </thead>


                <tbody id="tabelaProdutos">


                <?php

                if ($resultado && mysqli_num_rows($resultado) > 0) {

                    while ($produto = mysqli_fetch_assoc($resultado)) {

                        ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($produto['codigoDeBarras']) ?>
                            </td>


                            <td>

                                <?= htmlspecialchars($produto['nome']) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars($produto['categoria']) ?>

                            </td>


                            <td>

                                <span
                                    class="<?= $produto['estoque'] <= 5
                                        ? 'estoque-baixo'
                                        : 'estoque-normal' ?>"
                                >

                                    <?= $produto['estoque'] ?>

                                </span>

                            </td>


                            <td style="width: 150px;">

                                <input
                                    type="number"
                                    min="1"
                                    class="form-control quantidade-adicionar"
                                    placeholder=""
                                >

                            </td>


                            <td>

    <div class="d-flex gap-2">

        <!-- ADICIONAR -->
        <button
            type="button"
            class="btn btn-success"
            onclick="adicionarEstoque(
                <?= $produto['idProduto'] ?>,
                this
            )"
        >
            <i class="bi bi-plus-lg"></i>
            Quantidade
        </button>


        <!-- RETIRAR -->
        <button
            type="button"
            class="btn btn-danger"
            onclick="retirarEstoque(
                <?= $produto['idProduto'] ?>,
                this
            )"
        >
            <i class="bi bi-dash-lg"></i>
            Retirar
        </button>

    </div>

</td>

                        </tr>

                        <?php

                    }

                } else {

                    ?>

                    <tr>

                        <td colspan="6" class="text-center">

                            Nenhum produto cadastrado.

                        </td>

                    </tr>

                    <?php

                }

                ?>


                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- =========================================
     POP-UP PERSONALIZADO DO ESTOQUE
========================================= -->

<div
    id="notificacaoOverlay"
    class="notificacao-overlay"
    role="dialog"
    aria-modal="true"
    aria-labelledby="notificacaoTitulo"
    aria-describedby="notificacaoMensagem"
>
    <div class="notificacao-card">

        <div id="notificacaoIcone" class="notificacao-icone info">
            <i class="bi bi-info-circle-fill"></i>
        </div>

        <h3 id="notificacaoTitulo" class="notificacao-titulo">
            Informação
        </h3>

        <p id="notificacaoMensagem" class="notificacao-mensagem"></p>

        <div class="notificacao-acoes">

            <button
                type="button"
                id="notificacaoCancelar"
                class="notificacao-botao secundario"
                style="display:none;"
            >
                Cancelar
            </button>

            <button
                type="button"
                id="notificacaoConfirmar"
                class="notificacao-botao principal"
            >
                Continuar
            </button>

        </div>

    </div>
</div>


<script>

function filtrarProdutos() {

    const busca =
        document
        .getElementById("busca")
        .value
        .toLowerCase();


    const linhas =
        document
        .querySelectorAll("#tabelaProdutos tr");


    linhas.forEach(function(linha) {

        const texto =
            linha.innerText.toLowerCase();


        if (texto.includes(busca)) {

            linha.style.display = "";

        } else {

            linha.style.display = "none";

        }

    });

}



// =========================================
// SISTEMA DE NOTIFICAÇÕES
// =========================================

let resolverNotificacaoEstoque = null;
let acaoNotificacaoEstoque = null;

function mostrarNotificacaoEstoque(
    tipo,
    titulo,
    mensagem,
    textoBotao = "Continuar",
    aoContinuar = null
) {
    const overlay = document.getElementById("notificacaoOverlay");
    const icone = document.getElementById("notificacaoIcone");
    const tituloEl = document.getElementById("notificacaoTitulo");
    const mensagemEl = document.getElementById("notificacaoMensagem");
    const confirmar = document.getElementById("notificacaoConfirmar");
    const cancelar = document.getElementById("notificacaoCancelar");

    if (resolverNotificacaoEstoque) {
        const resolverAnterior = resolverNotificacaoEstoque;
        resolverNotificacaoEstoque = null;
        resolverAnterior(false);
    }

    const configuracoes = {
        sucesso: ["sucesso", "bi-check-circle-fill"],
        erro: ["erro", "bi-exclamation-triangle-fill"],
        confirmacao: ["confirmacao", "bi-question-circle-fill"],
        info: ["info", "bi-info-circle-fill"]
    };

    const config = configuracoes[tipo] || configuracoes.info;

    icone.className = "notificacao-icone " + config[0];
    icone.replaceChildren();

    const i = document.createElement("i");
    i.className = "bi " + config[1];
    icone.appendChild(i);

    tituloEl.textContent = titulo;
    mensagemEl.textContent = mensagem;
    confirmar.textContent = textoBotao;
    cancelar.style.display = "none";
    confirmar.dataset.confirmacao = "false";

    confirmar.disabled = false;
    cancelar.disabled = false;

    acaoNotificacaoEstoque = aoContinuar;
    overlay.classList.add("visivel");
    confirmar.focus();
}

function fecharNotificacaoEstoque() {
    const overlay = document.getElementById("notificacaoOverlay");

    if (resolverNotificacaoEstoque) {
        const resolver = resolverNotificacaoEstoque;
        resolverNotificacaoEstoque = null;

        limparNotificacaoEstoque();
        overlay.classList.remove("visivel");
        resolver(false);
        return;
    }

    const acao = acaoNotificacaoEstoque;
    acaoNotificacaoEstoque = null;

    limparNotificacaoEstoque();
    overlay.classList.remove("visivel");

    if (typeof acao === "function") {
        acao();
    }
}

function limparNotificacaoEstoque() {
    const confirmar = document.getElementById("notificacaoConfirmar");
    const cancelar = document.getElementById("notificacaoCancelar");

    confirmar.onclick = null;
    cancelar.onclick = null;

    confirmar.dataset.confirmacao = "false";
    confirmar.disabled = false;
    cancelar.disabled = false;
    cancelar.style.display = "none";
}

function confirmarNotificacaoEstoque(titulo, mensagem) {
    return new Promise(resolve => {
        const overlay = document.getElementById("notificacaoOverlay");
        const icone = document.getElementById("notificacaoIcone");
        const tituloEl = document.getElementById("notificacaoTitulo");
        const mensagemEl = document.getElementById("notificacaoMensagem");
        const confirmar = document.getElementById("notificacaoConfirmar");
        const cancelar = document.getElementById("notificacaoCancelar");

        if (resolverNotificacaoEstoque) {
            const resolverAnterior = resolverNotificacaoEstoque;
            resolverNotificacaoEstoque = null;
            resolverAnterior(false);
        }

        acaoNotificacaoEstoque = null;

        icone.className = "notificacao-icone confirmacao";
        icone.replaceChildren();

        const i = document.createElement("i");
        i.className = "bi bi-question-circle-fill";
        icone.appendChild(i);

        tituloEl.textContent = titulo;
        mensagemEl.textContent = mensagem;

        confirmar.textContent = "Confirmar";
        cancelar.textContent = "Cancelar";
        cancelar.style.display = "inline-block";

        confirmar.dataset.confirmacao = "true";
        confirmar.disabled = false;
        cancelar.disabled = false;

        resolverNotificacaoEstoque = resolve;

        function concluir(resposta) {
            if (!resolverNotificacaoEstoque) return;

            const resolver = resolverNotificacaoEstoque;
            resolverNotificacaoEstoque = null;

            limparNotificacaoEstoque();
            overlay.classList.remove("visivel");

            resolver(resposta);
        }

        confirmar.onclick = () => concluir(true);
        cancelar.onclick = () => concluir(false);

        overlay.classList.add("visivel");
        confirmar.focus();
    });
}

document.getElementById("notificacaoConfirmar")
    .addEventListener("click", function () {
        if (this.dataset.confirmacao === "true") return;
        fecharNotificacaoEstoque();
    });

document.getElementById("notificacaoCancelar")
    .addEventListener("click", function () {
        if (resolverNotificacaoEstoque) {
            const confirmar = document.getElementById("notificacaoConfirmar");
            confirmar.disabled = false;
            this.disabled = false;
        }
    });

document.getElementById("notificacaoOverlay")
    .addEventListener("click", function (event) {
        if (event.target !== this) return;

        if (resolverNotificacaoEstoque) {
            document.getElementById("notificacaoCancelar").click();

            // Garante o cancelamento da confirmação.
            if (resolverNotificacaoEstoque) {
                const resolver = resolverNotificacaoEstoque;
                resolverNotificacaoEstoque = null;
                limparNotificacaoEstoque();
                this.classList.remove("visivel");
                resolver(false);
            }
        } else {
            fecharNotificacaoEstoque();
        }
    });

document.addEventListener("keydown", function (event) {
    if (event.key !== "Escape") return;

    const overlay = document.getElementById("notificacaoOverlay");
    if (!overlay.classList.contains("visivel")) return;

    event.preventDefault();

    if (resolverNotificacaoEstoque) {
        const resolver = resolverNotificacaoEstoque;
        resolverNotificacaoEstoque = null;

        limparNotificacaoEstoque();
        overlay.classList.remove("visivel");
        resolver(false);
    } else {
        fecharNotificacaoEstoque();
    }
});


// =========================================
// ATUALIZA VISUAL DO ESTOQUE
// =========================================

function atualizarVisualEstoque(linha, quantidade) {
    const estoque = linha.querySelector("td:nth-child(4) span");

    estoque.textContent = quantidade;
    estoque.className = quantidade <= 5
        ? "estoque-baixo"
        : "estoque-normal";
}


// =========================================
// ADICIONAR ESTOQUE
// =========================================

async function adicionarEstoque(idProduto, botao) {
    const linha = botao.closest("tr");
    const campo = linha.querySelector(".quantidade-adicionar");
    const quantidade = parseInt(campo.value, 10);

    if (!Number.isInteger(quantidade) || quantidade <= 0) {
        mostrarNotificacaoEstoque(
            "info",
            "Quantidade inválida",
            "Digite uma quantidade inteira maior que zero."
        );
        campo.focus();
        return;
    }

    const confirmado = await confirmarNotificacaoEstoque(
        "Adicionar ao estoque?",
        "Você está prestes a adicionar " + quantidade +
        " unidade(s) deste produto ao estoque.\n\nDeseja continuar?"
    );

    if (!confirmado) return;

    botao.disabled = true;

    try {
        const response = await fetch("atualizar_estoque.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                idProduto: idProduto,
                quantidade: quantidade
            })
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.sucesso) {
            mostrarNotificacaoEstoque(
                "erro",
                "Não foi possível atualizar",
                resultado.mensagem || "O estoque não foi atualizado."
            );
            return;
        }

        atualizarVisualEstoque(linha, resultado.estoque);
        campo.value = "";

        mostrarNotificacaoEstoque(
            "sucesso",
            "Estoque atualizado!",
            "Foram adicionadas " + quantidade +
            " unidade(s) ao estoque.\n\n" +
            "Nova quantidade em estoque: " + resultado.estoque + "."
        );

    } catch (error) {
        console.error("Erro ao adicionar estoque:", error);

        mostrarNotificacaoEstoque(
            "erro",
            "Erro de comunicação",
            "Não foi possível atualizar o estoque. Verifique o servidor e tente novamente."
        );
    } finally {
        botao.disabled = false;
    }
}


// =========================================
// RETIRAR ESTOQUE
// =========================================

async function retirarEstoque(idProduto, botao) {
    const linha = botao.closest("tr");
    const campo = linha.querySelector(".quantidade-adicionar");
    const quantidade = parseInt(campo.value, 10);

    if (!Number.isInteger(quantidade) || quantidade <= 0) {
        mostrarNotificacaoEstoque(
            "info",
            "Quantidade inválida",
            "Digite uma quantidade inteira maior que zero."
        );
        campo.focus();
        return;
    }

    const estoqueElement = linha.querySelector("td:nth-child(4) span");
    const estoqueAtual = parseInt(estoqueElement.textContent, 10);

    if (quantidade > estoqueAtual) {
        mostrarNotificacaoEstoque(
            "erro",
            "Estoque insuficiente",
            "Não é possível retirar " + quantidade +
            " unidade(s).\n\nEstoque disponível: " + estoqueAtual + "."
        );
        campo.focus();
        return;
    }

    const confirmado = await confirmarNotificacaoEstoque(
        "Retirar do estoque?",
        "Você está prestes a retirar " + quantidade +
        " unidade(s) deste produto.\n\nDeseja continuar?"
    );

    if (!confirmado) return;

    botao.disabled = true;

    try {
        const response = await fetch("retirar_estoque.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                idProduto: idProduto,
                quantidade: quantidade
            })
        });

        const resultado = await response.json();

        if (!response.ok || !resultado.sucesso) {
            mostrarNotificacaoEstoque(
                "erro",
                "Não foi possível retirar",
                resultado.mensagem || "O estoque não foi atualizado."
            );
            return;
        }

        atualizarVisualEstoque(linha, resultado.estoque);
        campo.value = "";

        mostrarNotificacaoEstoque(
            "sucesso",
            "Estoque atualizado!",
            "Foram retiradas " + quantidade +
            " unidade(s) do estoque.\n\n" +
            "Quantidade restante: " + resultado.estoque + "."
        );

    } catch (error) {
        console.error("Erro ao retirar estoque:", error);

        mostrarNotificacaoEstoque(
            "erro",
            "Erro de comunicação",
            "Não foi possível atualizar o estoque. Verifique o servidor e tente novamente."
        );
    } finally {
        botao.disabled = false;
    }
}




</script>


<script>
function alternarSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const conteudo = document.querySelector(".content");

    sidebar.classList.toggle("escondida");
    conteudo.classList.toggle("sidebar-fechada");

    const fechada = sidebar.classList.contains("escondida");
    localStorage.setItem("sidebarFechada", fechada);
}

document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.querySelector(".sidebar");
    const conteudo = document.querySelector(".content");
    const fechada = localStorage.getItem("sidebarFechada");

    if (fechada === "true") {
        sidebar.classList.add("escondida");
        conteudo.classList.add("sidebar-fechada");
    }
});
</script>
</body>
</html>

</body>

</html>