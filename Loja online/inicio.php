<?php
session_start();

// =====================================================
// CONEXÃO COM O BANCO
// =====================================================
$conn = new mysqli("localhost","root","","containerdoqueijo");
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// =====================================================
// PEGAR CATEGORIA DA URL
// =====================================================
$categoria = $_GET['categoria'] ?? '';

// =====================================================
// INICIALIZAR CARRINHO
// =====================================================
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// =====================================================
// FUNÇÃO PARA VOLTAR À PÁGINA
// =====================================================

function voltarPagina($categoria = ''){
    $url = "inicio.php";
    if ($categoria !== '') {
        $url .= "?categoria=" . urlencode($categoria);
    }
    header("Location: $url");
    exit;
}

// =====================================================
// ADICIONAR PRODUTO AO CARRINHO
// =====================================================
if (isset($_GET['adicionar'])) {
    $idProduto = (int) $_GET['adicionar'];
    $sql = "
        SELECT idProduto, nome, valor, imagem, categoria
        FROM produtos
        WHERE idProduto = ?
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erro ao preparar produto: " . $conn->error);
    }
    $stmt->bind_param("i", $idProduto);
    $stmt->execute();
    $resultadoProduto = $stmt->get_result();
    if ($produto = $resultadoProduto->fetch_assoc()) {
        if (isset($_SESSION['carrinho'][$idProduto])) {
            $_SESSION['carrinho'][$idProduto]['quantidade']++;
        } else {
            $_SESSION['carrinho'][$idProduto] = [
                'quantidade' => 1,
                'nome' => $produto['nome'],
                'valor' => $produto['valor'],
                'imagem' => $produto['imagem']
            ];
        }
    }
    voltarPagina($categoria);
}

// =====================================================
// AUMENTAR QUANTIDADE
// =====================================================
if (isset($_GET['aumentar'])) {
    $idProduto = (int) $_GET['aumentar'];
    if (isset($_SESSION['carrinho'][$idProduto])) {
        $_SESSION['carrinho'][$idProduto]['quantidade']++;
    }
    voltarPagina($categoria);
}

// =====================================================
// DIMINUIR QUANTIDADE
// =====================================================
if (isset($_GET['diminuir'])) {
    $idProduto = (int) $_GET['diminuir'];
    if (isset($_SESSION['carrinho'][$idProduto])) {
        $_SESSION['carrinho'][$idProduto]['quantidade']--;
        // Se chegar a zero, remove
        if ($_SESSION['carrinho'][$idProduto]['quantidade'] <= 0) {
            unset($_SESSION['carrinho'][$idProduto]);
        }
    }
    voltarPagina($categoria);
}

// =====================================================
// REMOVER PRODUTO
// =====================================================
if (isset($_GET['remover'])) {
    $idProduto = (int) $_GET['remover'];
    if (isset($_SESSION['carrinho'][$idProduto])) {
        unset($_SESSION['carrinho'][$idProduto]);
    }
    voltarPagina($categoria);
}

// =====================================================
// LIMPAR CARRINHO
// =====================================================
if (isset($_GET['limpar'])) {
    $_SESSION['carrinho'] = [];
    voltarPagina($categoria);
}

// =====================================================
// BUSCAR PRODUTOS
// =====================================================
if ($categoria !== '') {
    $sql = "
        SELECT idProduto, nome, valor, imagem, categoria
        FROM produtos
        WHERE categoria = ?
        ORDER BY nome ASC
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erro ao preparar consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $categoria);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $sql = "
        SELECT idProduto, nome, valor, imagem, categoria
        FROM produtos
        ORDER BY nome ASC
    ";
    $resultado = $conn->query($sql);
    if (!$resultado) {
        die("Erro na consulta: " . $conn->error);
    }
}

// =====================================================
// MONTAR ARRAY
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
<table class="topo">
    <tr>
        <td>
            <img id="logo" src="../Imagens/Logo.jpeg" style="height: 40px; width: 90px; margin-left: 20px; margin-right: 90px">
            <input type="text" id="pesquisa" name="pesquisa" class="form-control barra_pesquisa" placeholder="Queijos, Doces, Defumados e Iguarias" autofocus>
        </td>
        <td>
            <button type="button" class="btn_topo" onclick="alterar_div()" id="btn_carrinho">
                <img id="icon_carrinho" src="../Imagens/Carrinho.png" style="height:40px; width:40px;">
                <br>
                <label>Carrinho</label>
            </button>
        </td>
        <td style="width:5%;">
            <button type="button" class="btn-abrir" onclick="abrirPopuplogin() ">
                <img id="cliente" src="../Imagens/Cliente.png" style="height:40px;">
                <label>Login</label>
            </button>
        </td>
    </tr>
</table>
<!-- =====================================================
    Janela do Popup Login
===================================================== -->
<div id="modalFundoLogin" class="modal-fundo">
    <div class="modal">
        <button type="button"class="btn-fechar"onclick="fecharPopuplogin()">
            ×
        </button>
        <form action="verificacao_cadastro_cliente.php" method="post">
            <div>
                <table align="center">
                    <tr>
                        <td align="center" colspan="2">
                            <h1>
                                Entre na sua conta
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            CPF:
                        </th>
                        <td>
                            <input type="text" id="cpf" name="cpf" maxlength="14" oninput="mascaraCPF(this)"  placeholder="000.000.000-00" required>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Senha:
                        </th>

                        <td>
                            <input type="password" name="senha" id="senha_tela_de_login" placeholder="Digite sua senha" required>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <button type="submit" value="Entrar">
                                Entrar
                            </button> 
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <button type="button" class="btn-abrir" onclick="abrirPopupcadastro() ">
                                <label>Não possuo uma conta</label>
                            </button>
                        </td>
                    </tr>
                </table> 
            </div>
        </form>
    </div>
</div>
<!-- =====================================================
    Janela do Popup Login
===================================================== -->
<div id="modalFundoCadastro" class="modal-fundo" align="center">
    <div class="modal">
        <button type="button"class="btn-fechar"onclick="fecharPopupcadastro()">
            ×
        </button>
        <form action="cadastro_cliente.php" method="post">
            <div>
                <table>
                        <tr> 
                            <td align="center" colspan="2">
                                <h1>
                                    Crie sua conta
                                </h1>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>Nome:</label>
                            </th>
                            <td>
                                <input type="text" name="nome" placeholder="Digite seu Nome:" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>E-mail:</label>
                            </th>
                            <td>
                                <input type="email" name="email" placeholder="Digite seu E-mail" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>CPF:</label>
                            </th>
                            <td>
                                <input type="text" id="cpf" name="cpf" maxlength="14" oninput="mascaraCPF(this)"  placeholder="000.000.000-00" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>Senha:</label>
                            </th>
                            <td>
                                <input type="password" name="senha" placeholder="Digite sua senha" required>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                ENDEREÇO
                            </th>
                        </tr>
                        <tr>
                            <th>
                                CEP:
                            </th>
                            <td>
                                <input type="text" name="cep" id="cep" placeholder="Digite o CEP do seu endereço" required maxlength="9" oninput="mascaraCEP(this)">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>Rua:</label>
                            </th>
                            <td>
                                <input type="text" name="rua" placeholder="Digite sua Rua" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>Número:</label>
                            </th>
                            <td>
                                <input type="number" name="numero" placeholder="Digite o número" required>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label>Bairro:</label>
                            </th>
                                <td>
                                    <input type="text" name="bairro" placeholder="Digite seu Bairro" required>
                                </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <button type="submit" onclick="vazio()"> 
                                    Cadastrar
                                </button>
                            </td>
                        </tr>
                </table>
            </div>
        </form>
    </div>
</div>
<!-- =====================================================
    CATEGORIAS
===================================================== -->
<ul id="lista-categorias">
    <li>
        <button type="button" onclick="alterarCategoria('')">
            Todos
        </button>
    </li>
    <li>
        <button type="button" onclick="alterarCategoria('Promoções')">
            Promoções
        </button>
    </li>
    <li>
        <button type="button" onclick="alterarCategoria('Queijos')">
            Queijos
        </button>
    </li>
    <li>
        <button type="button" onclick="alterarCategoria('Defumados')">
            Defumados
        </button>
    </li>
    <li>
        <button type="button" onclick="alterarCategoria('Doces')">
            Doces
        </button>
    </li>
    <li>
        <button type="button" onclick="alterarCategoria('Bebidas')">
            Bebidas
        </button>
    </li>
</ul>

<!-- =====================================================
     ÁREA PRINCIPAL
===================================================== -->
<div class="produtos_categorias_carrinho">
<!-- =================================================
     PRODUTOS
================================================= -->
    <div class="produtos_categorias">
<!-- =================================================
     CARROSSEL
================================================= -->
        <div id="centro">
            <div class="slider">
                <div class="slides">
                    <img src="../Imagens/FotosQueijos/f1.webp" alt="imagem 1" class="slide active accordion reajuste">
                    <img src="../Imagens/FotosQueijos/f2.webp" alt="imagem 2" class="slide reajuste">
                    <img src="../Imagens/FotosQueijos/f3.webp" alt="imagem 3" class="slide reajuste">
                </div>
                <div class="indicators">
                    <span class="dot active" data-index="0"></span>
                    <span class="dot" data-index="1"></span>
                    <span class="dot" data-index="2"></span>
                </div>
            </div>
        </div>
<!-- =================================================
     PRODUTOS
================================================= -->
        <div>
            <div class="carrinho-container">
                <?php if (!empty($itens)): ?>
                    <?php foreach ($itens as $key => $value): ?>
                        <div class="produto">
                            <!-- IMAGEM -->
                            <img src="../Produtos/<?php echo htmlspecialchars($value['imagem']); ?>" style=" height:220px; width:220px; object-fit:contain; " alt="<?php echo htmlspecialchars($value['nome']); ?>">
                            <br>
                            <!-- NOME -->
                            <strong>
                                <?php echo htmlspecialchars($value['nome']); ?>
                            </strong>
                            <br>
                            <!-- PREÇO -->
                            R$
                            <?php echo number_format((float)$value['valor'],2,',','.'); ?>
                            <br>
                            <!-- ADICIONAR -->
                            <a
                                href="?adicionar=<?php echo $key; ?><?php
                                    echo $categoria !== '' ? '&categoria=' . urlencode($categoria) : ''; ?>" >
                                Adicionar ao Carrinho
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div align="center" style="width:100%; padding:30px;" >
                        <strong>
                            Nenhum produto encontrado nesta categoria.
                        </strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<!-- =================================================
     CARRINHO
================================================= -->
    <div align="right" class="local_carrinho" id="carrinho">
        <div>
            <div class="carrinho">
<!-- =================================================
     LISTA DO CARRINHO
================================================= -->
                <div>
                    <h2 align="center">
                        Carrinho:
                    </h2>
                    <?php if (!empty($_SESSION['carrinho'])): ?>
                        <?php
                        $total = 0;
                        foreach ( $_SESSION['carrinho'] as $key => $value):
                            $subtotal = $value['quantidade'] * $value['valor'];
                            $total += $subtotal;
                        ?>
                            <div style=" padding:10px; margin:10px; border-bottom:1px solid #ccc; " align="center">
                                <strong>
                                    <?php echo htmlspecialchars($value['nome']);?>
                                </strong>
                                <br>
                                Quantidade:
                                <!-- DIMINUIR -->
                                <button type="button" onclick="tirar1(<?php echo $key; ?>)">
                                    -
                                </button>
                                <strong>
                                    <?php echo $value['quantidade']; ?>
                                </strong>
                                <!-- AUMENTAR -->
                                <button type="button" onclick="adicionar1(<?php echo $key; ?>)">
                                    +
                                </button>
                                <br>
                                Preço:
                                R$
                                <?php
                                echo number_format( $subtotal, 2, ',', '.');?>
                                <br><br>
                                <!-- REMOVER -->
                                <button type="button" onclick="apagaProduto(<?php echo $key; ?>)">
                                    Retirar do carrinho
                                </button>
                            </div>
                        <?php endforeach; ?>
<!-- =================================================
     LIMPAR
================================================= -->
                        <div align="center">
                            <button type="button" class="btn_carrinho" onclick="limparCarrinho()">
                                Limpar Carrinho
                            </button>
                        </div>
                    <?php else: ?>
                        <div align="center">
                            Carrinho vazio.
                        </div>
                    <?php endif; ?>
                </div>
<!-- =================================================
     FIM DA COMPRA
================================================= -->
                <div style="width:28vw">
                    <h2 align="center">
                        Fim da compra
                    </h2>
                    <?php if (!empty($_SESSION['carrinho'])): ?>
                        <?php
                        $totalResumo = 0;
                        foreach ($_SESSION['carrinho']as $value) {
                            $subtotal = $value['quantidade'] * $value['valor'];
                            $totalResumo += $subtotal;
                        }
                        ?>
                        <div align="center">
                            <h4>
                                Total:
                                R$
                                <?php echo number_format($totalResumo,2,',','.'); ?>
                            </h4>
                            <button type="button" class="btn_carrinho">
                                Comprar
                            </button>
                        </div>
                    <?php else: ?>
                        <div align="center">
                            Carrinho vazio.
                        </div>
                    <?php endif; ?>
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
    if (!area) {
        return;
    }
    if (
        area.classList.contains(
            "carrinho_aberto"
        )
    ) {
        area.classList.remove(
            "carrinho_aberto"
        );
        localStorage.setItem(
            "carrinhoAberto",
            "false"
        );
    } else {
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
    function() {
        const area =
            document.querySelector(".produtos_categorias_carrinho");
        if (!area) {
            return;
        }
        const estado =
            localStorage.getItem("carrinhoAberto");
        if (estado === "true") {
            area.classList.add("carrinho_aberto");
        } else {
            area.classList.remove("carrinho_aberto");
        }
    }
);

// =====================================================
// ALTERAR CATEGORIA
// =====================================================
function alterarCategoria(categoria) {
    if (categoria === "") {
        window.location.href = "inicio.php";
    } else {
        window.location.href = "inicio.php?categoria=" + encodeURIComponent(categoria);
    }
}

// =====================================================
// REMOVER PRODUTO
// =====================================================
function apagaProduto(id) {
    let categoria =
        "<?php echo htmlspecialchars($categoria, ENT_QUOTES); ?>";
    let url =
        "?remover=" + id;
    if (categoria !== "") {
        url += "&categoria=" + encodeURIComponent(categoria);
    }
    window.location.href = url;
}

// =====================================================
// LIMPAR CARRINHO
// =====================================================
function limparCarrinho() {
    let categoria = "<?php echo htmlspecialchars($categoria, ENT_QUOTES); ?>";
    let url = "?limpar=1";
    if (categoria !== "") {
        url += "&categoria=" + encodeURIComponent(categoria);
    }
    window.location.href = url;
}

// =====================================================
// DIMINUIR QUANTIDADE
// =====================================================
function tirar1(id) {
    let categoria = "<?php echo htmlspecialchars($categoria, ENT_QUOTES); ?>";
    let url = "?diminuir=" + id;
    if (categoria !== "") {
        url += "&categoria=" + encodeURIComponent(categoria);
    }
    window.location.href = url;
}

// =====================================================
// AUMENTAR QUANTIDADE
// =====================================================
function adicionar1(id) {
    let categoria = "<?php echo htmlspecialchars($categoria, ENT_QUOTES); ?>";
    let url = "?aumentar=" + id;
    if (categoria !== "") {
        url += "&categoria=" + encodeURIComponent(categoria);
    }
    window.location.href = url;
}

// =====================================================
// Popup login
// =====================================================
function abrirPopuplogin() {
        document.getElementById("modalFundoLogin").style.display = "flex";
    }
function fecharPopuplogin() {
    document.getElementById("modalFundoLogin").style.display = "none";
}
// Fechar clicando no fundo escuro
document.getElementById("modalFundoLogin").addEventListener("click", function(event) {
    if (event.target === this) {
        fecharPopup();
    }
});

// =====================================================
// Popup cadastro
// =====================================================
function abrirPopupcadastro() {
        document.getElementById("modalFundoCadastro").style.display = "flex";
    }
function fecharPopupcadastro() {
    document.getElementById("modalFundoCadastro").style.display = "none";
}
// Fechar clicando no fundo escuro
document.getElementById("modalFundoCadastro").addEventListener("click", function(event) {
    if (event.target === this) {
        fecharPopup();
    }
});

</script>