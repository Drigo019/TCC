<?php
session_start();
// =====================================================
// PRODUTOS
// =====================================================
$conn = new mysqli("localhost", "root", "", "containerdoqueijo");

if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// =====================================================
// BUSCAR PRODUTOS DO BANCO
// =====================================================

$sql = "SELECT idProduto, nome, valor, imagem
        FROM produtos
        ORDER BY nome ASC";

$resultado = $conn->query($sql);

$itens = [];

while ($produto = $resultado->fetch_assoc()) {

    $itens[$produto['idProduto']] = [
        'nome' => $produto['nome'],
        'valor' => $produto['valor'],
        'imagem' => $produto['imagem']
    ];
}
// =====================================================
// ADICIONAR PRODUTO
// =====================================================
if (isset($_GET['adicionar'])) {
    $idProduto = (int) $_GET['adicionar'];
    if (isset($itens[$idProduto])) {
        if (isset($_SESSION['carrinho'][$idProduto])) {
            $_SESSION['carrinho'][$idProduto]['quantidade']++;
        } else {
            $_SESSION['carrinho'][$idProduto] = array(
                'quantidade' => 1,
                'nome' => $itens[$idProduto]['nome'],
                'valor' => $itens[$idProduto]['valor']
            );
        }
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
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
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
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
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
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
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
// =====================================================
// LIMPAR CARRINHO
// =====================================================
if (isset($_GET['limpar'])) {
    unset($_SESSION['carrinho']);
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
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
            <div class="topo_div">
                <td style="width: 10%;">
                    <button type="button" class="btn_topo" onclick="window.location.href='inicio.php'">
                        <img src="../Imagens/Logo.jpeg" style="height: 100px; border-radius: 100px; margin-left: 50px; box-shadow: 0px 2px 8px rgba(255, 255, 255, 0.88);">
                    </button>
                </td>
            </div>
            <td style="width: 75%;" align="center">
                <div style="margin: 0;">
                    <div style=" display: flex; justify-content: center; align-items: center; height: 100%; margin: 0;">
                        <div class="btn">
                            <button type="button" onclick="window.location.href='Categorias/promocoes.php'">
                                <img src="../Imagens/Promocoes.png" style="height: 20px;">
                                <label>Promoções</label>
                            </button>
                        </div>
                        <div class="btn">
                            <button type="button" onclick="window.location.href='Categorias/queijos.php'">
                                <img src="../Imagens/Queijos.png" style="height: 20px;">
                                <br>
                                <label>Queijos</label>
                            </button>
                        </div>
                        <div class="btn">
                            <button type="button" onclick="window.location.href='Categorias/defumados.php'">
                                <img src="../Imagens/Defumados.png" style="height: 20px;">
                                <br>
                                <label>Defumados</label>
                            </button>
                        </div>
                        <div class="btn">
                            <button type="button" onclick="window.location.href='Categorias/doces.php'">
                                <img src="../Imagens/Pacoquinha.png" style="height: 20px;">
                                <br>
                                <label>Doces</label>
                            </button>
                        </div>
                        <div class="btn">
                            <button type="button" onclick="window.location.href='Categorias/bebidas.php'">
                                <img src="../Imagens/Bebidas.png" style="height: 20px;">
                                <br>
                                <label>Bebidas</label>
                            </button>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <button type="button" class="btn_topo" onclick="alterar_div()" style="margin-bottom: 20px;">
                    <img id="icon_carrinho" src="../Imagens/Carrinho.png" style="height: 40; width: 40px;">
                    <br>
                    <label>Carrinho</label>
                </button>
            </td>
            <td style="width: 5%;">
                <button type="button" class="btn_topo" onclick="window.location.href='login/Cliente.html'" style="margin-bottom: 20px;"> 
                    <img id="cliente" src="../Imagens/Cliente.png" style="height: 40px; width: 40px;" align="center">
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
            <!-- CATEGORIAS -->
            
            <!-- TÍTULO -->
            <div>
                <h1 align="center" class="titulo_categoria">
                    <u>Produtos</u>
                </h1>
            </div>
            <!-- =================================================
            CARROSSEL
        ================================================== -->
            <div id="centro" style="height: auto; min-height: 100px;">
                <div id="centro">
    <div class="slider" style="border-radius: 100px;">
        <div class="slides">
            <img src="../Imagens/FotosQueijos/queijo1.jpeg" alt="imagem 1" class="slide active accordion reajuste">
            <img src="../Imagens/carrosel.jpeg" alt="imagem 2" class="slide reajuste">
            <img src="../Imagens/carrosel2.jpeg" alt="imagem 3" class="slide reajuste">
        </div>

        <div class="indicators">
            <span class="dot active" data-index="0"></span>
            <span class="dot" data-index="1"></span>
            <span class="dot" data-index="2"></span>
        </div>
    </div>
</div>
            </div>
            <!-- =================================================
            PRODUTOS
        ================================================== -->
            <div>
                <div class="carrinho-container">
                    <?php foreach ($itens as $key => $value) { ?>
                        <div class="produto">
                            <!-- IMAGEM -->
                            <img
                                src="../Produtos/<?php echo htmlspecialchars($value['imagem']); ?>"
                                style="height: 220px; width: 220px; object-fit: contain;"
                                alt="<?php echo htmlspecialchars($value['nome']); ?>"
                            >
                            <br>
                            <!-- NOME -->
                            <strong>
                                <?php echo htmlspecialchars($value['nome']); ?>
                            </strong>
                            <br>
                            <!-- PREÇO -->
                            R$
                            <?php echo number_format($value['valor'],2,',','.');?>
                            <br>
                            <!-- ADICIONAR -->
                            <a href="?adicionar=<?php echo $key; ?>">
                                Adicionar ao Carrinho
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- =================================================
        CARRINHO
    ================================================== -->
        <div align="right" class="local_carrinho" id="carrinho"> 
            <div>
                <div class="carrinho" id="">
                    <!-- =====================================
                    CARRINHO
                ====================================== -->
                    <div>
                        <h2 align="center">
                            Carrinho:
                        </h2>
                        <?php
                        if (!empty($_SESSION['carrinho'])) {
                            $total = 0;
                            foreach ($_SESSION['carrinho'] as $key => $value) {
                                $subtotal = $value['quantidade'] * $value['valor'];
                                $total += $subtotal;
                        ?>
                                <div style=" padding: 10px; margin: 10px; border-bottom: 1px solid #ccc;" align="center">
                                    <strong>
                                        <?php echo $value['nome']; ?>
                                    </strong>
                                    <br>
                                    Quantidade:
                                    <button type="button"  onclick="tirar1(<?php echo $key; ?>)">
                                        -
                                    </button>
                                    <strong>
                                        <?php echo $value['quantidade']; ?>
                                    </strong>
                                    <button type="button"  onclick="adicionar1(<?php echo $key; ?>)">
                                        +
                                    </button>
                                    <br>
                                    Preço:
                                    R$
                                    <?php
                                    echo number_format($subtotal, 2, ',', '.'); ?>
                                    <br>
                                    <div>
                                        <div align="center">
                                            <button type="button"  onclick="apagaProduto(<?php echo $key; ?>)">
                                                Retirar do carrinho
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            </h4>
                            <!-- LIMPAR -->
                            <div align="center">
                                <button type="button"  class="btn_carrinho" onclick="limparCarrinho()">
                                    Limpar Carrinho
                                </button>
                            </div>
                        <?php
                        } else {
                            echo '
                        <div align="center">
                            Carrinho vazio.
                        </div>
                        ';
                        }
                        ?>
                    </div>
                    <!-- =====================================
                    Fim da compra
                ====================================== -->
                    <div style="width: 28vw">
                        <h2 align="center">
                            Fim da compra
                        </h2>
                        <div align="center" style=" border: 1px solid; width: 100%; ">

                        </div>
                        <?php
                        if (!empty($_SESSION['carrinho'])) {
                            $totalResumo = 0;
                            foreach (
                                $_SESSION['carrinho']
                                as $value
                            ) {
                                $subtotal = $value['quantidade'] * $value['valor'];
                                $totalResumo += $subtotal;
                            }
                        ?>
                            <div>
                                <div align="center">
                                    <h4>
                                        Total:
                                        R$
                                        <?php
                                        echo number_format($totalResumo, 2, ',', '.'); ?>
                                    </h4>
                                    <button type="button"  class="btn_carrinho">
                                        Comprar
                                    </button>
                                </div>
                            </div>
                        <?php
                        } else {
                            echo '
                        <div align="center">
                            Carrinho vazio.
                        </div>
                        ';
                        }
                        ?>
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

    // Verifica se está aberto
    if (area.classList.contains("carrinho_aberto")) {

        // FECHAR
        area.classList.remove("carrinho_aberto");

        localStorage.setItem(
            "carrinhoAberto",
            "false"
        );

    } else {

        // ABRIR
        area.classList.add("carrinho_aberto");

        localStorage.setItem(
            "carrinhoAberto",
            "true"
        );
    }
}


// =====================================================
// MANTER ESTADO APÓS RECARREGAR
// =====================================================

window.addEventListener("DOMContentLoaded", function () {

    const area = document.querySelector(
        ".produtos_categorias_carrinho"
    );

    const estado = localStorage.getItem(
        "carrinhoAberto"
    );

    if (estado === "true") {

        area.classList.add("carrinho_aberto");

    } else {

        area.classList.remove("carrinho_aberto");

    }

});

        // =====================================================
        // REMOVER PRODUTO
        // =====================================================
        function apagaProduto(id) {
            window.location.href = "?remover=" + id;
        }

        // =====================================================
        // LIMPAR CARRINHO
        // =====================================================
        function limparCarrinho() {
            window.location.href = "?limpar=1";
        }

        // =====================================================
        // DIMINUIR QUANTIDADE
        // =====================================================
        function tirar1(id) {
            window.location.href = "?diminuir=" + id;
        }

        // =====================================================
        // AUMENTAR QUANTIDADE
        // =====================================================
        function adicionar1(id) {
            window.location.href = "?aumentar=" + id;
        }
</script>
</body>
</htm