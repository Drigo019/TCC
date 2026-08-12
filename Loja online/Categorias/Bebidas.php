
<?php
// Inicia a sessão do PHP.
// A sessão permite guardar dados do usuário enquanto ele navega no site.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Container do Queijo</title>
    <link rel="stylesheet" href="../style_carrosel.css">
    <link rel="stylesheet" href="../css.css">
</head>
<body class="fundo">  
    <table style="width: 100%;">
        <tr>
            <td style="width: 10%;">
                <button onclick="window.location.href='inicio'"></button>
                <img src="../../Imagens/Logo.jpeg" style="height: 75px; border-radius: 100px;">
            </td>
            <td style="width: 75%;" align="center">
                <form accept="pesquisar.php" method="GET">
                    <button type="submit">🔍</button>
                    <input style="width: 50%; " type="text" name="pesquisa" placeholder="Queijos, Doces, Defumados e Iguarias">
                </form>
            </td>
            <td>
                <a href="../carrinho.php">
                <img id="cliente" src="../../Imagens/Carrinho2.png" style="height: 40px" align="center">
                </a>
            </td>
            <td style="width: 5%;">
                <img id="cliente" src="../../Imagens/Cliente.png" style="height: 40px;" align="center">
                <a id="login" href="../Login/cliente.html" align="center">login</a>
            </td>
        </tr> 
    </table>
    <script src="../script_carrosel.js"></script>
    <div style="margin: 0;">
        <div style="display: flex; justify-content:center; align-items: center; height: 100%; margin: 0;">
            <div class="btn">
                <button onclick="window.location.href='promocoes.php'">
                    <img src="../../Imagens/Promocoes.png" style="height: 30px;"> <br>
                    Promoções
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='queijos.php'">
                    <img src="../../Imagens/Queijos.png" style="height: 30px;"> <br>
                    Queijos
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='defumados.php'">
                    <img src="../../Imagens/Defumados.png" style="height: 30px;" > <br>
                    Defumados
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='doces.php'">
                    <img src="../../Imagens/Pacoquinha.png" style="height: 30px;" > <br>
                    Doces
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='bebidas.php'">
                    <img src="../../Imagens/Bebidas.png" style="height: 30px;" > <br>
                    Bebidas
                </button>
            </div>
        </div>
    </div>
    <div>
        <div class="carrinho-container">
            <?php
                // Array contendo todos os produtos
                $itens = array(

                    // Produto 1
                    ['nome' => 'Geleia de mocotó artesanal', 'imagem' => '../../Produtos/geleiaDeMocoto.jpeg', 'preco' => 20.00]
                );

                // foreach percorre todos os produtos do array
                foreach($itens as $key => $value){
            ?>
                    <!-- Caixa do produto -->
                    <div class="produto">

                        <!-- Imagem do produto -->
                        <img src="<?php echo $value['imagem']; ?>" style="height: 150px;"><br><br>

                        <!-- Nome do produto -->
                        <strong><?php echo $value['nome']; ?></strong><br>

                        <!-- Mostra o preço formatado -->
                        R$ <?php echo number_format($value['preco'],2,',','.'); ?><br><br>

                        <!-- Link para adicionar produto -->
                        <!-- O valor do produto vai pela URL -->
                        <a href="?adicionar=<?php echo $key; ?>">
                            Adicionar ao Carrinho
                        </a>
                    </div>
                    <?php 
                } 
                    ?>
        </div>
        <?php
            // Verifica se existe "adicionar" na URL
            if(isset($_GET['adicionar'])){
                // Converte o valor recebido para inteiro
                $idProduto = (int) $_GET['adicionar'];

                // Verifica se o produto existe no array
                if(isset($itens[$idProduto])){
                    // Verifica se o produto já está no carrinho
                    if(isset($_SESSION['carrinho'][$idProduto])){
                        // Soma +1 na quantidade
                        $_SESSION['carrinho'][$idProduto]['quantidade']++;
                    }else{
                        // Cria um novo produto no carrinho
                        $_SESSION['carrinho'][$idProduto] = array(
                            // Quantidade inicial
                            'quantidade' => 1,

                            // Nome do produto
                            'nome' => $itens[$idProduto]['nome'],

                            // Preço do produto
                            'preco' => $itens[$idProduto]['preco']
                        );
                    }

        // Exibe mensagem na tela
        echo '<script>alert("Produto adicionado ao carrinho!");</script>';

                }else{
                    // Caso tentem adicionar um produto inexistente
                    die("Você não pode adicionar um produto que não existe.");
                }
            }

        ?> 
    </div>
    <div>
    <script src="../script_carrosel.js"></script>
</body>
</html>
<style>
    /* Página */
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
    }
    body {
        background-image: url('../../Imagens/fundo.avif');
        background-size: cover; /* Faz a imagem cobrir toda a tela */
        background-position: center; /* Centraliza a imagem */
        background-repeat: no-repeat;
        background-attachment: fixed; /* Mantém a imagem estática ao rolar a página */
    }
    .btn
    {
        border: 10px solid transparent;
    }

    /* Topo */
    .topo{
        width: 100%;
        background-color: white;
        padding: 10px;
        box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
    }

    /* Botões das categorias */
    .menu{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin: 20px 0;
    }

    /* Produtos */
    .carrinho-container{
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
    }

    /* Card */
    .produto{
        width: 220px;
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    }

    /* Imagem do produto */
    .produto img{
        object-fit: cover;
        border-radius: 10px;
    }

    /* Botão adicionar */
    .produto a{
        display: inline-block;
        margin-top: 10px;
        padding: 10px 15px;
        background-color: #0d6efd;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .produto a:hover{
        background-color: #084298;
    }

    /* Carrinho */
    .carrinho{
        width: 90%;
        max-width: 800px;
        margin: 30px auto;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    }
</style>