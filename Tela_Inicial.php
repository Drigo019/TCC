
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
    <link rel="stylesheet" href="css.css">
</head>
<body>  
    <table style="width: 100%;">
        <tr>
            <td style="width: 10%;">
                <img src="Imagens/Logo.jpeg" style="height: 75px; border-radius: 100px;">
            </td>
            <td style="width: 2%;">
                <button style=" background: transparent; border: none; cursor: pointer;" onclick="pesquisa()">
                    <img id="lupa" src="Imagens/Lupa_pesquisa.png" style="height: 30px;">
                </button>
            </td>
            <td style="width: 75%;">
                 <input type="text" name="pesquisa" id="pesquisa" placeholder="Queijos, Doces, Defumados e Iguarias" style="height: 20px; width: 90%;">
            </td>
            <td>
                <a href="Carrinho.php">
                <img id="cliente" src="Imagens/Cliente.png" style="height: 40px;" align="center">
                </a>
            </td>
            <td style="width: 5%;">
                <img id="cliente" src="Imagens/Cliente.png" style="height: 40px;" align="center">
                <a id="login" href="Tela_Login.html" align="center">login</a>
            </td>
        </tr> 
    </table>
    <div>
        <div style="display: flex; justify-content:center; align-items: center; height: 100%;">
            <div class="btn">
                <button onclick="window.location.href='Categorias/Promocoes.html'">
                    <img src="Imagens/Promocoes.png" style="height: 30px;"> <br>
                    Promoções
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='Categorias/Queijos.html'">
                    <img src="Imagens/Queijos.png" style="height: 30px;"> <br>
                    Queijos
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='Categorias/Defumados.html'">
                    <img src="Imagens/Defumados.png" style="height: 30px;" > <br>
                    Defumados
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='Categorias/Doces.html'">
                    <img src="Imagens/Pacoquinha.png" style="height: 30px;" > <br>
                    Doces
                </button>
            </div>
            <div class="btn">
                <button onclick="window.location.href='Categorias/Vinhos.html'">
                    <img src="Imagens/Vinhos.png" style="height: 30px;" > <br>
                    Vinhos
                </button>
            </div>
        </div>
    </div>
    <div class="carrinho-container">
           <div class="carrinho-container">
                <?php
                    // Array contendo todos os produtos
                    $itens = array
                                (

                                    // Produto 1
                                    ['nome' => 'Curso 1', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00],

                                    // Produto 2
                                    ['nome' => 'Curso 2', 'imagem' => '../imagens/vinhos.png', 'preco' => 20.00],

                                    // Produto 3
                                    ['nome' => 'Curso 3', 'imagem' => '../imagens/vinhos.png', 'preco' => 30.00],

                                    // Produto 4
                                    ['nome' => 'Curso 4', 'imagem' => '../imagens/vinhos.png', 'preco' => 40.00],

                                    // Produto 5
                                    ['nome' => 'Curso 5', 'imagem' => '../imagens/vinhos.png', 'preco' => 50.00],
                                );

                    // foreach percorre todos os produtos do array
                    foreach($itens as $key => $value){

                ?>

                    <!-- Caixa do produto -->
                    <div class="produto">

                        <!-- Imagem do produto -->
                        <img src="../TCC/Imagens/Logo.jpeg" style="height: 150px;"><br><br>

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

                <?php } ?>

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
                        echo "<script>window.location.href='Carrinho.php';</script>";

                    }else{

                        // Caso tentem adicionar um produto inexistente
                        die("Você não pode adicionar um produto que não existe.");

                    }
                }

                ?>     
            </div>
        </div>
    <h1 align="center">
        Promoções 
    </h1>
    <div>
        
    </div>
    <style>
        /* Página */
        body{
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f5f5f5;
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
            width: 100%;
            height: 150px;
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

        /* Títulos */
        h1,h2{
            text-align: center;
        }
    </style>
</body>
</html>