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
    <title>Carrinho</title>
</head>
<body style="background-color:rgb(214, 214, 214);">
    <div style="margin-top: 100px;">
        <div id="carrinho" style="display: flex; justify-content: space-between;">
            <div style="margin-left: 50px; height: 100%; width: 40%; border-radius: 10px; background-color: white;">
                 <?php 
                 echo "<h2 style='margin-left: 10px'>Carrinho:</h2>";
                     if(isset($_GET['adicionar']))
                        {
                            // Converte o valor recebido para inteiro
                            $idProduto = (int) $_GET['adicionar'];

                            // Verifica se o produto existe no array
                            if(isset($itens[$idProduto]))
                                {
                                    // Verifica se o produto já está no carrinho
                                    if(isset($_SESSION['carrinho'][$idProduto]))
                                        {
                                            // Soma +1 na quantidade
                                            $_SESSION['carrinho'][$idProduto]['quantidade']++;
                                        }
                                    else
                                        {
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
                                }
                        }
                    if(isset($_SESSION['carrinho']))
                        {
                            // Variável para guardar total
                            $total = 0;

                            // Percorre todos os produtos do carrinho
                            foreach($_SESSION['carrinho'] as $key => $value)
                                {
                                    // Multiplica quantidade pelo preço
                                    $subtotal = $value['quantidade'] * $value['preco'];

                                    // Soma no total geral
                                    $total += $subtotal;

                                    // Mostra os dados do produto
                                    echo '<p style="margin-left: 10px; width: 90%;">
                                        Nome: '.$value['nome'].' <br>
                                        Quantidade: '.$value['quantidade'].'
                                        <button style="margin-left: 20px;" onclick="apagaProduto()">❌</button> <br>
                                        Preço: R$ '.number_format($subtotal,2,',','.').'
                                        </p>';

                                }

                            // Mostra o valor total do carrinho
                            echo "<h3 style='margin-left: 10px'>Total: R$ ".number_format($total,2,',','.')."</h3>";
                        }
                    else
                        {
                            // Caso não exista nenhum produto
                            echo "Carrinho vazio.";
                        }    
                    ?>
            </div>
            <div style="margin-right: 50px;height: 100%; width: 40%; border-radius: 10px; background-color: white;">
                <h1 align="center">
                    Resumo da Compra
                </h1>
                <div align="center" style="font-size: 20px;">
                    -------------------------------------------------------------------
                </div>
                <?php
                if(isset($_SESSION['carrinho']))
                        {
                            // Variável para guardar total
                            $total = 0;

                            // Percorre todos os produtos do carrinho
                            foreach($_SESSION['carrinho'] as $key => $value)
                                {
                                    // Multiplica quantidade pelo preço
                                    $subtotal = $value['quantidade'] * $value['preco'];

                                    // Soma no total geral
                                    $total += $subtotal;
                                }
                            // Mostra o endereço
                            echo '<p style="margin-left: 20px;"> Endereço: '. '</p>';

                            // Mostra a taxa de entrega
                            echo '<p style="margin-left: 20px;"> Taxa de entrega: '. '</p>';
                            
                            // Mostra o valor total do carrinho
                            echo "<h3 style='margin-left: 20px;'>Total: R$ ".number_format($total,2,',','.')."</h3>";

                            

                            echo '<button style="margin-left: 40px; margin-bottom: 10px; font-size: 18px; width: 90%;"> comprar </button>';
                        }
                    else
                        {
                            // Caso não exista nenhum produto
                            echo "Carrinho vazio.";
                        }    
                    ?>
            </div>
        </div>
    </div>
    <script>
        function apagaProduto()
            {

            }
    </script>
</body>
</html>