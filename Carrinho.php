<?php
// Inicia a sessão do PHP.
// A sessão permite guardar dados do usuário enquanto ele navega no site.
session_start();
?>
<h2>Carrinho:</h2> 

    <?php
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
        // Verifica se existe carrinho na sessão
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
                        echo '<p>
                            Nome: '.$value['nome'].' |
                            Quantidade: '.$value['quantidade'].' |
                            Preço: R$ '.number_format($subtotal,2,',','.')
                            .'</p>';
                    }

                // Mostra o valor total do carrinho
                echo "<h3>Total: R$ ".number_format($total,2,',','.')."</h3>";
            }
        else
            {
                // Caso não exista nenhum produto
                echo "Carrinho vazio.";
            }
    ?>