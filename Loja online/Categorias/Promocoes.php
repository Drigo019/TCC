
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
    <link rel="stylesheet" href="../style.css">
</head>
<body class="body">
    <table class="table">
        <tr>
            <td style="width: 10%;">
                <img src="../../Imagens/Logo.jpeg" style="height: 75px; border-radius: 100px;">
            </td>
            <td style="width: 75%;" align="center">
                <form accept="pesquisar.php" method="GET">
                    <button type="submit" style="height: 30px;">🔍</button>
                    <input style="width: 50%; height: 25px" type="text" name="pesquisa" placeholder="Como posso te fazer feliz hoje?">
                </form>
            </td>
            <td>
                <a href="../../Loja online/carrinho.php">
                <img id="cliente" src="../../Imagens/Carrinho2.png" style="height: 40px" align="center">
                </a>
            </td>
            <td style="width: 5%;">
                <img id="cliente" src="../../Imagens/Cliente.png" style="height: 40px;" align="center">
                <a id="login" href="../../Loja online/Login/cliente.html" align="center">login</a>
            </td>
        </tr> 
    </table>
    <div style="margin: 0;">
        <div>
            <div class="categorias">
                <div>
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
    </div>
    <div>
        <h1 align="center" style="color: black;">
            Promoções 
        </h1>
    </div>
    <div>
        <div class="container-produtos">
            <?php
                // Array contendo todos os produtos
                $itens = array(
                    
                    // Produto 1
                    ['nome' => 'Geleia de mocotó artesanal', 'imagem' => '../../Produtos/geleiaDeMocoto.jpeg', 'preco' => 20.00],

                    // Produto 2
                    ['nome' => 'Queijo trufado peça de 500g', 'imagem' => '../../Produtos/QueijoTrufado.jpeg', 'preco' => 30.00],

                    // Produto 3
                    ['nome' => 'Trufado com azeitona', 'imagem' => '../../Produtos/trufadoComAzeitona.jpeg', 'preco' => 30.00],

                    // Produto 4
                    ['nome' => 'Mussarela fatiada ou pedaço', 'imagem' => '../../Produtos/mussarelafatiada.jpeg', 'preco' => 39.99],

                    // Produto 5
                    ['nome' => 'Fresco de Monte Belo', 'imagem' => '../../Produtos/FrescoDeMonteBelo.jpeg', 'preco' => 24.90],

                     // Produto 6
                    ['nome' => 'Majestic', 'imagem' => '../../Produtos/Majestic.jpeg', 'preco' => 37.00],

                    // Produto 7
                    ['nome' => 'Provolone desidratado', 'imagem' => '../../Produtos/provoloneDesidratado.jpeg', 'preco' => 19.90],

                    // Produto 8
                    ['nome' => 'Queijo Holandês lemmender', 'imagem' => '../../Produtos/queijoHolandesLemmender.jpeg', 'preco' => 79.90],

                    // Produto 9
                    ['nome' => 'Caixa de paçoxa com 100 unidades', 'imagem' => '../../Produtos/caixaDePacoca.jpeg', 'preco' => 19.99],

                    // Produto 10
                    ['nome' => 'Apresuntado Aurora', 'imagem' => '../../Produtos/apresuntadoAurora.jpeg', 'preco' => 22.00],

                     // Produto 11
                    ['nome' => 'Parmesão', 'imagem' => '../../Produtos/parmesao.jpeg', 'preco' => 76.90],

                    // Produto 12
                    ['nome' => 'Salame vila caipira', 'imagem' => '../../Produtos/SalameVilaCaipira.jpeg', 'preco' => 19.90],

                    // Produto 13
                    ['nome' => 'Provolone artesanal peça de 300g', 'imagem' => '../../Produtos/ProvoloneArtesanal.jpeg', 'preco' => 19.90],

                    // Produto 14
                    ['nome' => 'Queijo canastra', 'imagem' => '../../Produtos/queijoCanastra.jpeg', 'preco' => 49.90],

                    // Produto 15
                    ['nome' => 'Doce de leite em pedaços', 'imagem' => '../../Produtos/doceDeLeite.jpeg', 'preco' => 19.90],
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
    <script src="script_carrosel.js"></script>
</body>
</html>