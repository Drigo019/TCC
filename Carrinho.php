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
                    ['imagem' => 'logo.jpeg','preco' => 10.00], 
                    ['imagem' => 'logo.jpeg','preco' => 10.00], 
                    ['imagem' => 'logo.jpeg','preco' => 10.00]
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
                    if(isset($_SESSION['idproduto']))
                        
                }     
            else
                {
                    die("Você não pode adicionar um produto que não existe");
                }
        }
?>
</body>
</html>

