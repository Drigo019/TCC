</php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de compras</title>
    <style type="text/css">
        .carrinho-container{
            display: flex;
            flex-wrap: wrap;
        }
        .produto{
            width: 200px;
            height: 175px;
            border: 1px solid #000;
            margin: 10px;
            text-align: center;
        }  
    </style>
</head>
<body>
    <h2>Carrinho de compras</h2>
    <div class="carrinho-container"> 
    
<?php

    $itens = array
                (
                    ['nome' => 'Curso 1', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00],
                    ['nome' => 'Curso 2', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00],
                    ['nome' => 'Curso 3', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00],
                    ['nome' => 'Curso 4', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00],
                    ['nome' => 'Curso 5', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00],
                    ['nome' => 'Curso 6', 'imagem' => '../imagens/vinhos.png', 'preco' => 10.00]
                    
                );
    foreach($itens as $key => $value)
        {
?>
    <div class="produto">
        <img src="../TCC/Imagens/Logo.jpeg" style="height: 150px;">
        <a href="?adicionar=<?php echo $key; ?>"> Adicionar ao Carrinho</a>
    </div> <!--produto-->
<?php
        }
?>

    </div> <!--carrinho-container-->


<?php
    if(isset($_GET['adicionar']))
        {
            $idProduto = (int) $_GET['adicionar'];
            if(isset($itens[$idProduto]))
                {
                    if(isset($_SESSION['carrinho']['idproduto']))
                        {
                            $_SESSION['carrinho'][$idProduto]['quantdade']++;
                        }
                    else 
                        {
                           $_SESSION['carrinho'][$idProduto] = array('quantidade' => 1, "nome" => $itens[$idProduto]["nome"], 'preco' => $itens[$idProduto]['preco']);
                        }
                    echo '<script> alert("Produto adicionado ao carrinho"); </script>';   
                }     
            else
                {
                    die("Você não pode adicionar um produto que não existe");
                }
        }
?>

    <h2>
        Carrinho:
    </h2>
    <?php
        foreach($_SESSION['carrinho'] as $key => $value)
            {
                //nome do produto

                //quantidade

                //preço
                echo '<p> Nome: '.$value['nome'].' | Quantidade: '.$value['quantidade'].' | Preço: '.($value['quantidade'] * $value['preco']).'</p>';
            }
    ?>
</body>
</html>

