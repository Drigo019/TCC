<?php
session_start();

// =====================================================
// CONEXÃO COM O BANCO
// =====================================================

$conn = new mysqli("localhost", "root", "", "containerdoqueijo");

if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


// =====================================================
// PEGAR CATEGORIA DA URL
// =====================================================

$categoria = $_GET['categoria'] ?? '';


// =====================================================
// BUSCAR PRODUTOS DO BANCO
// =====================================================

if ($categoria !== '') {

    // Busca somente os produtos da categoria selecionada
    $sql = "SELECT idProduto, nome, valor, imagem, categoria
            FROM produtos
            WHERE categoria = ?
            ORDER BY nome ASC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro ao preparar consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $categoria);

    if (!$stmt->execute()) {
        die("Erro ao executar consulta: " . $stmt->error);
    }

    $resultado = $stmt->get_result();

} else {

    // Se nenhuma categoria foi selecionada,
    // mostra todos os produtos
    $sql = "SELECT idProduto, nome, valor, imagem, categoria
            FROM produtos
            ORDER BY nome ASC";

    $resultado = $conn->query($sql);

    if (!$resultado) {
        die("Erro na consulta: " . $conn->error);
    }
}


// =====================================================
// MONTAR ARRAY DE PRODUTOS
// =====================================================

$itens = [];

while ($produto = $resultado->fetch_assoc()) {

    $itens[$produto['idProduto']] = [
        'nome' => $produto['nome'],
        'valor' => $produto['valor'],
        'imagem' => $produto['imagem'],
        'categoria' => $produto['categoria']
    ];
}


// =====================================================
// ADICIONAR PRODUTO AO CARRINHO
// =====================================================

if (isset($_GET['adicionar'])) {

    $idProduto = (int) $_GET['adicionar'];

    // Busca o produto novamente caso ele não esteja
    // na categoria atualmente selecionada
    $sqlProduto = "SELECT idProduto, nome, valor, imagem, categoria
                   FROM produtos
                   WHERE idProduto = ?";

    $stmtProduto = $conn->prepare($sqlProduto);
    $stmtProduto->bind_param("i", $idProduto);
    $stmtProduto->execute();

    $resultadoProduto = $stmtProduto->get_result();

    if ($produto = $resultadoProduto->fetch_assoc()) {

        if (isset($_SESSION['carrinho'][$idProduto])) {

            $_SESSION['carrinho'][$idProduto]['quantidade']++;

        } else {

            $_SESSION['carrinho'][$idProduto] = [
                'quantidade' => 1,
                'nome' => $produto['nome'],
                'valor' => $produto['valor']
            ];
        }
    }

    // Volta para a página mantendo a categoria
    $urlVoltar = "inicio.php";

    if ($categoria !== '') {
        $urlVoltar .= "?categoria=" . urlencode($categoria);
    }

    header("Location: " . $urlVoltar);
    exit;
}


// =====================================================
// AUMENTAR QUANTIDADE
// =====================================================

if (isset($_GET['aumentar'])) {

    $idProduto = (int) $_GET['aumentar'];

    if (isset($_SESSION['carrinho'][$idProduto])) {

        $_SESSION['carrinho'][$idProduto]['quantidade']++;
    }

    $urlVoltar = "inicio.php";

    if ($categoria !== '') {
        $urlVoltar .= "?categoria=" . urlencode($categoria);
    }

    header("Location: " . $urlVoltar);
    exit;
}


// =====================================================
// DIMINUIR QUANTIDADE
// =====================================================

if (isset($_GET['diminuir'])) {

    $idProduto = (int) $_GET['diminuir'];

    if (isset($_SESSION['carrinho'][$idProduto])) {

        $_SESSION['carrinho'][$idProduto]['quantidade']--;

        if ($_SESSION['carrinho'][$idProduto]['quantidade'] <= 0) {

            unset($_SESSION['carrinho'][$idProduto]);
        }
    }

    $urlVoltar = "inicio.php";

    if ($categoria !== '') {
        $urlVoltar .= "?categoria=" . urlencode($categoria);
    }

    header("Location: " . $urlVoltar);
    exit;
}


// =====================================================
// REMOVER PRODUTO
// =====================================================

if (isset($_GET['remover'])) {

    $idProduto = (int) $_GET['remover'];

    if (isset($_SESSION['carrinho'][$idProduto])) {

        unset($_SESSION['carrinho'][$idProduto]);
    }

    $urlVoltar = "inicio.php";

    if ($categoria !== '') {
        $urlVoltar .= "?categoria=" . urlencode($categoria);
    }

    header("Location: " . $urlVoltar);
    exit;
}


// =====================================================
// LIMPAR CARRINHO
// =====================================================

if (isset($_GET['limpar'])) {

    unset($_SESSION['carrinho']);

    $urlVoltar = "inicio.php";

    if ($categoria !== '') {
        $urlVoltar .= "?categoria=" . urlencode($categoria);
    }

    header("Location: " . $urlVoltar);
    exit;
}

?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Container do Queijo</title>

    <link rel="stylesheet" href="style_carrosel.css">

    <link rel="stylesheet" href="style.css">

</head>


<body class="fundo">


<!-- =====================================================
     TOPO
===================================================== -->

<table class="topo" id="topo">

    <tr>

        <!-- LOGO -->

        <td style="width: 10%;">

            <button
                type="button"
                class="btn_topo"
                onclick="window.location.href='inicio.php'"
            >

                <img
                    src="../Imagens/Logo2.png"
                    style="
                        height: 110px;
                        border-radius: 10px;
                        margin-left: 50px;
                    "
                >

            </button>

        </td>


        <!-- =================================================
             CATEGORIAS
        ================================================== -->

        <td style="width: 75%;" align="center">

            <div style="margin: 0;">

                <div
                    style="
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100%;
                        margin: 0;
                    "
                >


                    <!-- PROMOÇÕES -->

                    <div class="btn">

                        <button
                            type="button"
                            onclick="alterar_categoria('promocoes')"
                        >

                            <img
                                src="../Imagens/Promocoes.png"
                                style="height: 20px;"
                            >

                            <label>Promoções</label>

                        </button>

                    </div>


                    <!-- QUEIJOS -->

                    <div class="btn">

                        <button
                            type="button"
                            onclick="alterar_categoria('queijos')"
                        >

                            <img
                                src="../Imagens/Queijos.png"
                                style="height: 20px;"
                            >

                            <br>

                            <label>Queijos</label>

                        </button>

                    </div>


                    <!-- DEFUMADOS -->

                    <div class="btn">

                        <button
                            type="button"
                            onclick="alterar_categoria('defumados')"
                        >

                            <img
                                src="../Imagens/Defumados.png"
                                style="height: 20px;"
                            >

                            <br>

                            <label>Defumados</label>

                        </button>

                    </div>


                    <!-- DOCES -->

                    <div class="btn">

                        <button
                            type="button"
                            onclick="alterar_categoria('doces')"
                        >

                            <img
                                src="../Imagens/Pacoquinha.png"
                                style="height: 20px;"
                            >

                            <br>

                            <label>Doces</label>

                        </button>

                    </div>


                    <!-- BEBIDAS -->

                    <div class="btn">

                        <button
                            type="button"
                            onclick="alterar_categoria('bebidas')"
                        >

                            <img
                                src="../Imagens/Bebidas.png"
                                style="height: 20px;"
                            >

                            <br>

                            <label>Bebidas</label>

                        </button>

                    </div>


                </div>

            </div>

        </td>


        <!-- =================================================
             CARRINHO
        ================================================== -->

        <td>

            <button
                type="button"
                class="btn_topo"
                onclick="alterar_div()"
                style="
                    margin-bottom: 20px;
                    margin-right: 40px;
                "
            >

                <img
                    id="icon_carrinho"
                    src="../Imagens/Carrinho.png"
                    style="
                        height: 40px;
                        width: 40px;
                    "
                >

                <br>

                <label>Carrinho</label>

            </button>

        </td>


        <!-- =================================================
             LOGIN
        ================================================== -->

        <td style="width: 5%;">

            <button
                type="button"
                class="btn_topo"
                onclick="window.location.href='login/Cliente.html'"
                style="
                    margin-bottom: 20px;
                    margin-right: 80px;
                "
            >

                <img
                    id="cliente"
                    src="../Imagens/Cliente.png"
                    style="
                        height: 40px;
                        width: 40px;
                    "
                >

                <label>login</label>

            </button>

        </td>

    </tr>

</table>



<!-- =====================================================
     ÁREA PRINCIPAL
===================================================== -->

<div class="produtos_categorias_carrinho">


    <!-- =================================================
         PRODUTOS
    ================================================== -->

    <div class="produtos_categorias">


        <!-- TÍTULO -->

        <div>

            <h1
                align="center"
                class="titulo_categoria"
            >

                <u>Produtos</u>

            </h1>

        </div>



        <!-- =================================================
             CARROSSEL
        ================================================== -->

        <div
            id="centro"
            style="
                height: auto;
                min-height: 90px;
            "
        >

            <div id="centro">

                <div
                    class="slider"
                    style="border-radius: 100px;"
                >

                    <div class="slides">

                        <img
                            src="../Imagens/FotosQueijos/queijo1.jpeg"
                            alt="imagem 1"
                            class="slide active accordion reajuste"
                        >

                        <img
                            src="../Imagens/carrosel.jpeg"
                            alt="imagem 2"
                            class="slide reajuste"
                        >

                        <img
                            src="../Imagens/carrosel2.jpeg"
                            alt="imagem 3"
                            class="slide reajuste"
                        >

                    </div>


                    <div class="indicators">

                        <span
                            class="dot active"
                            data-index="0"
                        ></span>

                        <span
                            class="dot"
                            data-index="1"
                        ></span>

                        <span
                            class="dot"
                            data-index="2"
                        ></span>

                    </div>

                </div>

            </div>

        </div>



        <!-- =================================================
             LISTA DE PRODUTOS
        ================================================== -->

        <div>

            <div class="carrinho-container">


                <?php if (!empty($itens)) { ?>


                    <?php foreach ($itens as $key => $value) { ?>


                        <div class="produto">


                            <!-- IMAGEM -->

                            <img
                                src="../Produtos/<?php echo htmlspecialchars($value['imagem']); ?>"
                                style="
                                    height: 200px;
                                    width: 220px;
                                    object-fit: contain;
                                "
                                alt="<?php echo htmlspecialchars($value['nome']); ?>"
                            >

                            <br>


                            <!-- NOME -->

                            <strong>

                                <?php
                                echo htmlspecialchars($value['nome']);
                                ?>

                            </strong>

                            <br>


                            <!-- PREÇO -->

                            R$

                            <?php

                            echo number_format(
                                $value['valor'],
                                2,
                                ',',
                                '.'
                            );

                            ?>

                            <br>


                            <!-- ADICIONAR -->

                            <a
                                href="?adicionar=<?php echo $key; ?><?php
                                    echo $categoria !== ''
                                        ? '&categoria=' . urlencode($categoria)
                                        : '';
                                ?>"
                            >

                                Adicionar ao Carrinho

                            </a>


                        </div>


                    <?php } ?>


                <?php } else { ?>


                    <!-- =================================================
                         NENHUM PRODUTO
                    ================================================== -->

                    <div
                        align="center"
                        style="
                            width: 100%;
                            padding: 30px;
                        "
                    >

                        <h2>

                            Nenhum produto encontrado
                            nesta categoria.

                        </h2>

                    </div>


                <?php } ?>


            </div>

        </div>

    </div>



    <!-- =================================================
         CARRINHO
    ================================================== -->

    <div
        align="right"
        class="local_carrinho"
        id="carrinho"
    >

        <div>

            <div class="carrinho">


                <!-- =================================================
                     ITENS DO CARRINHO
                ================================================== -->

                <div>

                    <h2 align="center">

                        Carrinho:

                    </h2>


                    <?php if (!empty($_SESSION['carrinho'])) { ?>


                        <?php

                        $total = 0;

                        foreach (
                            $_SESSION['carrinho']
                            as $key => $value
                        ) {

                            $subtotal =
                                $value['quantidade']
                                *
                                $value['valor'];

                            $total += $subtotal;

                        ?>



                            <div
                                style="
                                    padding: 10px;
                                    margin: 10px;
                                    border-bottom: 1px solid #ccc;
                                "
                                align="center"
                            >


                                <!-- NOME -->

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $value['nome']
                                    );
                                    ?>

                                </strong>

                                <br>


                                <!-- QUANTIDADE -->

                                Quantidade:


                                <button
                                    type="button"
                                    onclick="tirar1(<?php echo $key; ?>)"
                                >

                                    -

                                </button>


                                <strong>

                                    <?php
                                    echo $value['quantidade'];
                                    ?>

                                </strong>


                                <button
                                    type="button"
                                    onclick="adicionar1(<?php echo $key; ?>)"
                                >

                                    +

                                </button>

                                <br>


                                <!-- PREÇO -->

                                Preço:

                                R$

                                <?php

                                echo number_format(
                                    $subtotal,
                                    2,
                                    ',',
                                    '.'
                                );

                                ?>

                                <br>


                                <!-- REMOVER -->

                                <div>

                                    <div align="center">

                                        <button
                                            type="button"
                                            onclick="apagaProduto(<?php echo $key; ?>)"
                                        >

                                            Retirar do carrinho

                                        </button>

                                    </div>

                                </div>


                            </div>


                        <?php } ?>


                        <!-- =================================================
                             TOTAL
                        ================================================== -->

                        <div align="center">

                            <h3>

                                Total:

                                R$

                                <?php

                                echo number_format(
                                    $total,
                                    2,
                                    ',',
                                    '.'
                                );

                                ?>

                            </h3>

                        </div>


                        <!-- =================================================
                             LIMPAR
                        ================================================== -->

                        <div align="center">

                            <button
                                type="button"
                                class="btn_carrinho"
                                onclick="limparCarrinho()"
                            >

                                Limpar Carrinho

                            </button>

                        </div>


                    <?php } else { ?>


                        <div align="center">

                            Carrinho vazio.

                        </div>


                    <?php } ?>


                </div>



                <!-- =================================================
                     FIM DA COMPRA
                ================================================== -->

                <div style="width: 28vw">


                    <h2 align="center">

                        Fim da compra

                    </h2>


                    <div
                        align="center"
                        style="
                            border: 1px solid;
                            width: 100%;
                        "
                    >

                    </div>


                    <?php if (!empty($_SESSION['carrinho'])) { ?>


                        <?php

                        $totalResumo = 0;

                        foreach (
                            $_SESSION['carrinho']
                            as $value
                        ) {

                            $subtotal =
                                $value['quantidade']
                                *
                                $value['valor'];

                            $totalResumo += $subtotal;

                        }

                        ?>


                        <div>

                            <div align="center">

                                <h4>

                                    Total:

                                    R$

                                    <?php

                                    echo number_format(
                                        $totalResumo,
                                        2,
                                        ',',
                                        '.'
                                    );

                                    ?>

                                </h4>


                                <button
                                    type="button"
                                    class="btn_carrinho"
                                >

                                    Comprar

                                </button>

                            </div>

                        </div>


                    <?php } else { ?>


                        <div align="center">

                            Carrinho vazio.

                        </div>


                    <?php } ?>


                </div>


            </div>

        </div>

    </div>

</div>



<!-- =====================================================
     JAVASCRIPT DO CARROSSEL
===================================================== -->

<script src="script_carrosel.js"></script>


<script>

// =====================================================
// ABRIR / FECHAR CARRINHO
// =====================================================

function alterar_div() {

    const area = document.querySelector(
        ".produtos_categorias_carrinho"
    );


    if (area.classList.contains("carrinho_aberto")) {

        // FECHAR

        area.classList.remove(
            "carrinho_aberto"
        );


        localStorage.setItem(
            "carrinhoAberto",
            "false"
        );


    } else {

        // ABRIR

        area.classList.add(
            "carrinho_aberto"
        );


        localStorage.setItem(
            "carrinhoAberto",
            "true"
        );

    }

}



// =====================================================
// MANTER ESTADO DO CARRINHO
// =====================================================

window.addEventListener(
    "DOMContentLoaded",
    function () {

        const area =
            document.querySelector(
                ".produtos_categorias_carrinho"
            );


        const estado =
            localStorage.getItem(
                "carrinhoAberto"
            );


        if (estado === "true") {

            area.classList.add(
                "carrinho_aberto"
            );

        } else {

            area.classList.remove(
                "carrinho_aberto"
            );

        }

    }
);



// =====================================================
// REMOVER PRODUTO
// =====================================================

function apagaProduto(id) {

    let url = "?remover=" + id;

    <?php if ($categoria !== '') { ?>

        url += "&categoria=<?php
            echo urlencode($categoria);
        ?>";

    <?php } ?>

    window.location.href = url;
}



// =====================================================
// LIMPAR CARRINHO
// =====================================================

function limparCarrinho() {

    let url = "?limpar=1";

    <?php if ($categoria !== '') { ?>

        url += "&categoria=<?php
            echo urlencode($categoria);
        ?>";

    <?php } ?>

    window.location.href = url;
}



// =====================================================
// DIMINUIR QUANTIDADE
// =====================================================

function tirar1(id) {

    let url = "?diminuir=" + id;

    <?php if ($categoria !== '') { ?>

        url += "&categoria=<?php
            echo urlencode($categoria);
        ?>";

    <?php } ?>

    window.location.href = url;
}



// =====================================================
// AUMENTAR QUANTIDADE
// =====================================================

function adicionar1(id) {

    let url = "?aumentar=" + id;

    <?php if ($categoria !== '') { ?>

        url += "&categoria=<?php
            echo urlencode($categoria);
        ?>";

    <?php } ?>

    window.location.href = url;
}



// =====================================================
// ALTERAR CATEGORIA
// =====================================================

function alterar_categoria(categoria) {

    let url =
        "inicio.php?categoria="
        +
        encodeURIComponent(categoria);

    window.location.href = url;
}

</script>


</body>

</html>