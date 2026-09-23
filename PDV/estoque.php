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
        180deg,
        #111827,
        #1f2937
    );

    padding: 30px 20px;

    border-radius: 0 20px 20px 0;

    box-shadow: 5px 0 20px rgba(0,0,0,0.1);
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

</style>

</head>


<body>


<!-- MENU -->

<div class="sidebar">

    <h2 class="logo">
        Container do Queijo
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

    <a href="funcionarios.html">
        <i class="bi bi-people"></i>
        Funcionários
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


function adicionarEstoque(idProduto, botao) {

    const linha =
        botao.closest("tr");


    const campo =
        linha.querySelector(
            ".quantidade-adicionar"
        );


    const quantidade =
        parseInt(campo.value);


    if (!quantidade || quantidade <= 0) {

        alert(
            "Digite uma quantidade válida."
        );

        campo.focus();

        return;

    }


    const confirmar =
        confirm(
            "Adicionar " +
            quantidade +
            " unidade(s) ao estoque?"
        );


    if (!confirmar) {

        return;

    }


    fetch("atualizar_estoque.php", {

        method: "POST",

        headers: {

            "Content-Type":
                "application/json"

        },

        body: JSON.stringify({

            idProduto: idProduto,

            quantidade: quantidade

        })

    })

    .then(response => response.json())

    .then(resultado => {

        if (!resultado.sucesso) {

            alert(
                "Erro: " +
                resultado.mensagem
            );

            return;

        }


        const estoque =
            linha.querySelector(
                "td:nth-child(4) span"
            );


        estoque.textContent =
            resultado.estoque;


        if (resultado.estoque <= 5) {

            estoque.className =
                "estoque-baixo";

        } else {

            estoque.className =
                "estoque-normal";

        }


        campo.value = "";


        alert(
            "Estoque atualizado com sucesso!"
        );

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro de comunicação com o servidor."
        );

    });

}

function retirarEstoque(idProduto, botao) {

const linha =
    botao.closest("tr");


const campo =
    linha.querySelector(
        ".quantidade-adicionar"
    );


const quantidade =
    parseInt(campo.value);


/* ================================
   VALIDA QUANTIDADE
================================= */

if (!quantidade || quantidade <= 0) {

    alert(
        "Digite uma quantidade válida."
    );

    campo.focus();

    return;
}


/* ================================
   PEGA O ESTOQUE ATUAL
================================= */

const estoqueElement =
    linha.querySelector(
        "td:nth-child(4) span"
    );


const estoqueAtual =
    parseInt(
        estoqueElement.textContent
    );


/* ================================
   VERIFICA ESTOQUE
================================= */

if (quantidade > estoqueAtual) {

    alert(
        "Não é possível retirar " +
        quantidade +
        " unidade(s).\n\n" +
        "Estoque disponível: " +
        estoqueAtual
    );

    campo.focus();

    return;
}


/* ================================
   CONFIRMAÇÃO
================================= */

const confirmar =
    confirm(
        "Retirar " +
        quantidade +
        " unidade(s) do estoque?"
    );


if (!confirmar) {

    return;

}


/* ================================
   ENVIA PARA O PHP
================================= */

fetch(
    "retirar_estoque.php",
    {

        method: "POST",

        headers: {

            "Content-Type":
                "application/json"

        },

        body: JSON.stringify({

            idProduto: idProduto,

            quantidade: quantidade

        })

    }
)


.then(
    response =>
        response.json()
)


.then(resultado => {


    /* ================================
       ERRO
    ================================= */

    if (!resultado.sucesso) {

        alert(
            "Erro: " +
            resultado.mensagem
        );

        return;
    }


    /* ================================
       ATUALIZA ESTOQUE NA TELA
    ================================= */

    estoqueElement.textContent =
        resultado.estoque;


    /* ================================
       ATUALIZA A COR
    ================================= */

    if (
        resultado.estoque <= 5
    ) {

        estoqueElement.className =
            "estoque-baixo";

    } else {

        estoqueElement.className =
            "estoque-normal";

    }


    /* ================================
       LIMPA CAMPO
    ================================= */

    campo.value = "";


    alert(
        "Estoque retirado com sucesso!"
    );

})


.catch(error => {

    console.error(error);

    alert(
        "Erro de comunicação com o servidor."
    );

});

}

</script>


</body>

</html>