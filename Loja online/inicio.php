        <?php

        session_start();

        // Recuperar a posição da tela após atualizar o carrinho.
$posicaoRolagem = $_SESSION['posicao_rolagem'] ?? null;
unset($_SESSION['posicao_rolagem']);

        $notificacaoCompra = $_SESSION['notificacao'] ?? null;
unset($_SESSION['notificacao']);

        // =====================================================
        // CONEXÃO COM O BANCO
        // =====================================================
        $conn = new mysqli("localhost","root","","containerdoqueijo", 3308);
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



function voltarPagina($categoria = '') {
    $url = "inicio.php";

    if ($categoria !== '') {
        $url .= "?categoria=" . urlencode($categoria);
    }

    if (isset($_GET['adicionar'])) {
        $url .= (strpos($url, '?') !== false ? '&' : '?')
              . 'restaurar_rolagem=1';
    }

    header("Location: " . $url);
    exit;
}



        
/* =====================================================
   EXIGIR LOGIN ANTES DE ALTERAR O CARRINHO
===================================================== */

if (
    (isset($_GET['adicionar']) || isset($_GET['aumentar']))
    && empty($_SESSION['cliente_id'])
) {
    $_SESSION['mensagem_login'] =
        "Apenas usuários cadastrados podem realizar compras. Entre na sua conta ou cadastre-se.";

    $_SESSION['abrir_login'] = true;

    voltarPagina($categoria);
}


        // =====================================================
        // CONTADOR DO CARRINHO
        // =====================================================
        $quantidadeCarrinho = 0;

        foreach ($_SESSION['carrinho'] as $produtoCarrinho) {
            $quantidadeCarrinho += (int)$produtoCarrinho['quantidade'];
        }


        // =====================================================
        // FUNÇÃO PARA VOLTAR À PÁGINA
        // =====================================================

        // =====================================================
        // ADICIONAR PRODUTO AO CARRINHO
        // =====================================================
        if (isset($_GET['adicionar'])) {

            $idProduto = (int) $_GET['adicionar'];

            $sql = "
                SELECT idProduto, nome, valor, imagem, categoria, estoque
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

                $estoque = (int) $produto['estoque'];

                // Quantidade que já está no carrinho
                $quantidadeAtual = 0;

                if (isset($_SESSION['carrinho'][$idProduto])) {
                    $quantidadeAtual =
                        (int) $_SESSION['carrinho'][$idProduto]['quantidade'];
                }

                // Verificar estoque
                if ($quantidadeAtual < $estoque) {

                    if ($quantidadeAtual > 0) {

                        $_SESSION['carrinho'][$idProduto]['quantidade']++;

                    } else {

                        $_SESSION['carrinho'][$idProduto] = [
                            'quantidade' => 1,
                            'nome' => $produto['nome'],
                            'valor' => $produto['valor'],
                            'imagem' => $produto['imagem']
                        ];
                    }

                } else {

                    $_SESSION['mensagem_estoque'] =
                        "Você já adicionou todo o estoque disponível.";
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

                // Buscar o estoque atual no banco
                $sql = "SELECT estoque FROM produtos WHERE idProduto = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $idProduto);
                $stmt->execute();

                $resultadoEstoque = $stmt->get_result();

                if ($produtoEstoque = $resultadoEstoque->fetch_assoc()) {

                    $estoque = (int) $produtoEstoque['estoque'];

                    $quantidadeAtual =
                        (int) $_SESSION['carrinho'][$idProduto]['quantidade'];

                    // VERIFICA SE AINDA TEM ESTOQUE
                    if ($quantidadeAtual < $estoque) {

                        $_SESSION['carrinho'][$idProduto]['quantidade']++;

                    } else {

                        $_SESSION['mensagem_estoque'] =
                            "Você já adicionou todo o estoque disponível.";
                    }
                }
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

            
<style>
/* ===== JANELA PERSONALIZADA ===== */
.modal-notificacao {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.65);
    backdrop-filter: blur(5px);
    z-index: 99999;
    padding: 20px;
    animation: aparecerFundo .25s ease;
}

.caixa-notificacao {
    background: #fff;
    width: 100%;
    max-width: 420px;
    border-radius: 22px;
    padding: 32px 28px;
    text-align: center;
    box-shadow: 0 20px 70px rgba(0,0,0,.3);
    animation: aparecerCaixa .3s ease;
}

.icone-notificacao {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    font-weight: bold;
}

.icone-sucesso {
    background: #e0f7e9;
    color: #168447;
}

.icone-erro {
    background: #fde8e7;
    color: #c0392b;
}

.icone-aviso {
    background: #fff3cd;
    color: #a66a00;
}

.icone-info {
    background: #e5f1ff;
    color: #1769aa;
}

.caixa-notificacao h2 {
    color: #222;
    font-size: 24px;
    margin: 0 0 12px;
}

.caixa-notificacao p {
    color: #666;
    font-size: 15px;
    line-height: 1.6;
    margin: 0 0 25px;
    white-space: pre-line;
}

.botoes-notificacao {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
}

.botao-notificacao {
    border: none;
    border-radius: 12px;
    padding: 13px 20px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: .2s;
}

.botao-notificacao:hover {
    transform: translateY(-2px);
    filter: brightness(.96);
}

.botao-principal {
    background: #f2c230;
    color: #29220d;
}

.botao-secundario {
    background: #eee;
    color: #444;
}


/* Botão Adicionar ao Carrinho — estilo original */
.produto .adicionar-ao-carrinho {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 15px;
    background-color: #e0b52f;
    text-decoration: none;
    border-radius: 5px;
    color: #000000;
    border: 1px solid #aaa;
    font-size: 17px;
    font-family: Arial, sans-serif;
    cursor: pointer;
    transition: 0.2s ease;
}

/* Efeito original ao passar o mouse */
.produto .adicionar-ao-carrinho:hover {
    background: #333;
    color: white;
    transform: translateY(-2px);
}

/* Aparência durante o carregamento */
.produto .adicionar-ao-carrinho:disabled {
    opacity: 0.7;
    cursor: wait;
    transform: none;
}


@keyframes aparecerFundo {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes aparecerCaixa {
    from {
        opacity: 0;
        transform: translateY(15px) scale(.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@media (max-width: 480px) {
    .caixa-notificacao {
        padding: 26px 20px;
    }

    .botoes-notificacao {
        flex-direction: column;
    }

    .botao-notificacao {
        width: 100%;
    }
}
</style>

        </head>
        <body class="fundo">
            <?php if (isset($_SESSION['mensagem_estoque'])): ?>

                <div class="mensagem-estoque">
                    <?php
                    echo htmlspecialchars($_SESSION['mensagem_estoque']);
                    unset($_SESSION['mensagem_estoque']);
                    ?>
                </div>

            <?php endif; ?>
        <!-- =====================================================
            TOPO
        ===================================================== -->

        
        <table class="topo">
            <tr>
                <td rowspan="2">
                    <img id="logo" src="../Imagens/Logo.jpeg" style="height: 80px; width: 160px; margin-left: 20px;">
                </td>
                <td>
                  <input type="text" id="pesquisa" name="pesquisa"
       class="barra_pesquisa"
       placeholder="Queijos, Doces, Defumados e Iguarias">
                </td>
                <td rowspan="2">
                    <button type="button" class="btn_topo" onclick="alterar_div()" id="btn_carrinho">
                        <img id="icon_carrinho" src="../Imagens/Carrinho.png" style="height:40px; width:40px;">
                        <span id="contador_carrinho">
                            <?php echo $quantidadeCarrinho; ?>
                        </span>
                    </button>
                </td>
                


<td style="width:5%;" rowspan="2">
    <?php if (!empty($_SESSION['cliente_id'])): ?>

        <button type="button"
                class="btn_cliente"
                onclick="confirmarLogout()">

            <img id="cliente"
                 src="../Imagens/Cliente.png"
                 alt="Cliente">

            <span>
                <?= htmlspecialchars(
                    $_SESSION['nome_usuario'] ?? 'Cliente',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

        </button>

    <?php else: ?>

        <button type="button"
                class="btn_cliente"
                onclick="abrirPopuplogin()">

            <img id="cliente"
                 src="../Imagens/Cliente.png"
                 alt="Cliente">

            <span>Cliente</span>

        </button>

    <?php endif; ?>
</td>



            </tr>
            <!-- =====================================================
                CATEGORIAS
            ===================================================== -->
            <tr id="lista-categorias" >
                <td style="border-left: 15px solid;">
                    <button type="button" onclick="alterarCategoria('')">
                        Todos
                    </button>
                    <button type="button" onclick="alterarCategoria('Promoções')">
                        Promoções
                    </button>
                    <button type="button" onclick="alterarCategoria('Queijos')">
                        Queijos
                    </button>
                    <button type="button" onclick="alterarCategoria('Defumados')">
                        Defumados
                    </button>
                    <button type="button" onclick="alterarCategoria('Doces')">
                        Doces
                    </button>
                    <button type="button" onclick="alterarCategoria('Bebidas')">
                        Bebidas
                    </button>
                </td>
            </tr>
        </table>
        <!-- =====================================================
            Janela do Popup Login
        ===================================================== -->
        <div id="modalFundoLogin" class="modal-fundo">
            <div class="modal popup-compra popup-login">
            <button type="button" class="btn-fechar" onclick="fecharPopuplogin()">
                ×
            </button>
            <h1>Entre na sua conta</h1>
            <?php if (isset($_SESSION['mensagem_login'])): ?>
        <p style="color: #c0392b; text-align: center;">
            <?= htmlspecialchars($_SESSION['mensagem_login']) ?>
        </p>
        <?php unset($_SESSION['mensagem_login']); ?>
    <?php endif; ?>
            <form action="verificacao_cadastro_cliente.php" method="post">
                <div class="dados-compra">
                    <label for="cpf">CPF:</label>
                    <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="000.000.000-00" oninput="mascaraCPF(this)" required>
                    <label for="senha_tela_de_login">Senha:</label>
                    <input type="password" name="senha" id="senha_tela_de_login" placeholder="Digite sua senha" required>
                    <button type="submit" class="btn-finalizar-compra">
                        Entrar
                    </button>
                    <button type="button" class="btn-cancelar-compra" onclick="abrirPopupcadastro()">
                        Não possuo uma conta
                    </button>
                </div>
            </form>
        </div>
    </div>

    </div>
        </div>
        <!-- =====================================================
            Janela do Popup Login
        ===================================================== -->
        <div id="modalFundoCadastro" class="modal-fundo">
            <div class="modal popup-compra popup-cadastro">
                <button type="button" class="btn-fechar" onclick="fecharPopupcadastro()">
                    ×
                </button>
                <h1>Crie sua conta</h1>
                <form action="cadastro_cliente.php" method="post">
                    <h2>Dados pessoais</h2>
                    <div class="dados-compra">
                        <label for="nomeCadastro">Nome:</label>
                        <input type="text" id="nomeCadastro" name="nome" placeholder="Digite seu nome" required>
                        <label for="emailCadastro">E-mail:</label>
                        <input type="email" id="emailCadastro" name="email" placeholder="Digite seu e-mail" required>
                        <label for="cpfCadastro">CPF:</label>
                        <input type="text" name="cpf" id="cpf" maxlength="14" placeholder="000.000.000-00" oninput="mascaraCPF(this)" required>
                        <label for="senhaCadastro">Senha:</label>
                        <input type="password" id="senhaCadastro" name="senha" placeholder="Digite sua senha" required>
                    </div>
                    <h2>Endereço</h2>
                    <div class="dados-compra">
                        <label for="cep">CEP:</label>
                        <input type="text" name="cep" id="cep" placeholder="Digite o CEP do seu endereço" maxlength="9" oninput="mascaraCEP(this)" required>
                        <label for="rua">Rua:</label>
                        <input type="text" name="rua" id="rua" placeholder="Digite sua rua" required>
                        <label for="numero">Número:</label>
                        <input type="number" name="numero" id="numero" placeholder="Digite o número" required>
                        <label for="bairro">Bairro:</label>
                        <input type="text" name="bairro" id="bairro" placeholder="Digite seu bairro" required>
                    </div>
                    <div class="botoes-finalizacao">
                        <button type="submit" class="btn-finalizar-compra" onclick="vazio()">
                            Cadastrar
                        </button>
                        <button type="button" class="btn-cancelar-compra" onclick="fecharPopupcadastro()">
                            Voltar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- =====================================================
            POPUP FINALIZAÇÃO DA COMPRA
        ===================================================== -->
        <div id="modalFundoFimCompra" class="modal-fundo">
            <div class="modal popup-compra">
                <!-- BOTÃO FECHAR -->
                <button type="button" class="btn-fechar" onclick="fecharPopupFimCompra()">
                    ×
                </button>
                <h1>Finalizar Compra</h1>
                    <!-- =================================================
                        RESUMO DOS PRODUTOS
                    ================================================= -->
                    <h2>Resumo do pedido</h2>
                  <div class="resumo-produtos" id="resumo-produtos-dinamico">
                        <?php if (!empty($_SESSION['carrinho'])): ?>
                            <?php $totalFinal = 0; ?>
                            <?php foreach ($_SESSION['carrinho'] as $key => $produto): ?>
                                <?php $subtotalProduto = $produto['quantidade'] * $produto['valor']; $totalFinal += $subtotalProduto; ?>
                                <div class="item-resumo">
                                    <!-- IMAGEM -->
                                    <img src="../Produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>" class="imagem-resumo">
                                    <div class="info-resumo">
                                        <strong>
                                            <?php echo htmlspecialchars($produto['nome']); ?>
                                        </strong>
                                        <span>
                                            Quantidade:
                                            <?php echo $produto['quantidade']; ?>
                                        </span>
                                        <span>
                                            Valor:
                                            R$
                                            <?php
                                            echo number_format( $produto['valor'], 2, ',', '.');?>
                                        </span>
                                        <strong>
                                            Subtotal:
                                            R$
                                            <?php echo number_format( $subtotalProduto, 2, ',', '.' ); ?>
                                        </strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Carrinho vazio.</p>
                        <?php endif; ?>
                    </div>
                    <!-- =================================================
                        TOTAL
                    ================================================= -->
                   <div class="total-final" id="total-final-dinamico">
                        <strong>
                            Total da compra:
                        </strong>
                        <strong>
                            R$
                            <?php echo number_format( $totalFinal ?? 0, 2, ',', '.');?>
                        </strong>
                    </div>
                    <!-- =================================================
                        DADOS DO CLIENTE
                    ================================================= -->
                    <h2>Dados do cliente</h2>
                    <form method="post" action="finalizar_compra.php">
                <div class="dados-compra">
                    <label for="nomeCliente">
                        Nome:
                    </label>
                    <input type="text" id="nomeCliente" name="nome" placeholder="Digite seu nome" required>
                    <label for="telefoneCliente">
                        Telefone:
                    </label>
                    <input type="text" name="telefone" id="telefone" maxlength="15" placeholder="(00) 00000-0000" oninput="mascaraTelefone(this)" required>
                </div>
                <!-- =================================================
                    FORMA DE PAGAMENTO
                ================================================= -->
                <h2>Forma de pagamento</h2>
                
<div class="forma-pagamento">
    <label for="form_pag_base">
        Escolha a forma de pagamento:
    </label>

    <select id="form_pag_base" required>
        <option value="">Selecione uma opção</option>
        <option value="Dinheiro">💵 Dinheiro</option>
        <option value="Cartão">💳 Cartão</option>
        <option value="Pix">📱 Pix</option>
        <option value="Crediário">🧾 Crediário</option>
    </select>

    <!-- Aparece somente quando Cartão for selecionado -->
    <div id="opcoesCartao" style="display: none; margin-top: 12px;">
        <label for="tipoCartao">Tipo de cartão:</label>

        <select id="tipoCartao">
            <option value="">Selecione Débito ou Crédito</option>
            <option value="Debito">Débito</option>
            <option value="Credito">Crédito</option>
        </select>
    </div>

    <!-- Valor que será enviado ao finalizar_compra.php -->
    <input type="hidden" name="form_pag" id="form_pag" value="">
</div>

                <!-- =================================================
                    CARRINHO EM JSON
                ================================================= -->
                <input type="hidden" name="carrinho" id="carrinhoFinal" value='
                    <?php
                        echo htmlspecialchars(
                            json_encode(
                                array_map(
                                    function($id, $produto) {
                                        return [
                                            "idProduto" => (int)$id,
                                            "quantidade" => (int)$produto["quantidade"]
                                        ];
                                    },
                                    array_keys($_SESSION["carrinho"]),
                                    $_SESSION["carrinho"]
                                ),
                                JSON_UNESCAPED_UNICODE
                            ),
                            ENT_QUOTES,
                            "UTF-8"
                        );
                    ?>'
                >
                <!-- =================================================
                    BOTÕES
                ================================================= -->
                <div class="botoes-finalizacao">
                    <button type="submit" class="btn-finalizar-compra">
                        Finalizar Compra
                    </button>
                    <button type="button" class="btn-cancelar-compra" onclick="fecharPopupFimCompra()">
                        Voltar
                    </button>
                </div>
                </form>
            </div>
        </div>
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
                    <div class="carrinho_container">
                        <?php if (!empty($itens)): ?>
                            <?php foreach ($itens as $key => $value): ?>
                                <div class="produto" data-nome="<?php echo htmlspecialchars(strtolower($value['nome'])); ?>">
                                    <!-- IMAGEM -->
                                    <img src="../Produtos/<?php echo htmlspecialchars($value['imagem']); ?>" style="height:220px; width:220px;  object-fit:contain; " alt="<?php echo htmlspecialchars($value['nome']); ?>">
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
                                    <button
    type="button"
    class="adicionar-ao-carrinho"
    data-id="<?php echo (int)$key; ?>">
    Adicionar ao Carrinho
</button>
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
                <!-- =====================================================
                    RODAPÉ
                ===================================================== -->
                <footer class="rodape">
                    <div class="rodape-container">
                        <!-- INFORMAÇÕES DA LOJA -->
                        <div class="rodape-coluna">
                            <h3>Container do Queijo</h3>
                            <p>
                                Produtos selecionados para você degustar, descobrir e se apaixonar.
                            </p>
                        </div>
                        <!-- INFORMAÇÕES DO RESPONSÁVEL -->
                        <div class="rodape-coluna">
                            <h3>Responsável</h3>
                            <p><strong>Nome:</strong> Eric Lima da Silva </p>
                            <p><strong>CNPJ:</strong> 22.126.506/0001-84</p>
                        </div>
                        <!-- CONTATO -->
                        <div class="rodape-coluna">
                            <h3>Contato</h3>
                            <p>📱 (19) 99229-5270</p>
                            <p>📧 86ericsilva@gmail.com</p>
                        </div>
                        <!-- ENDEREÇO -->
                        <div class="rodape-coluna">
                            <h3>Onde estamos</h3>
                            <p>
                                Rua Cel Diogo, nº 1149<br>
                                Centro - Mococa/SP
                            </p>
                        </div>
                    </div>
                    <div class="rodape-inferior">
                        <p>
                            © <?php echo date('Y'); ?> Container do Queijo. Todos os direitos reservados.
                        </p>
                    </div>
                </footer>
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
                        <div id="lista-carrinho-dinamica">
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
                        <div style="width:28vw" id="resumo-carrinho-dinamico">
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
                                    <button type="button" onclick="abrirPopupFimCompra()" class="btn_carrinho">
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
        
<!-- JANELA DE NOTIFICAÇÕES PERSONALIZADAS -->
<div id="modalNotificacao" class="modal-notificacao"
     role="dialog" aria-modal="true"
     aria-labelledby="tituloNotificacao">

    <div class="caixa-notificacao">

        <div id="iconeNotificacao"
             class="icone-notificacao icone-info">i</div>

        <h2 id="tituloNotificacao">Atenção</h2>

        <p id="textoNotificacao">
            Confira as informações.
        </p>

        <div id="botoesNotificacao"
             class="botoes-notificacao"></div>

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

            // =====================================================
            // Popup fim de compra
            // =====================================================
           
function abrirPopupFimCompra() {
    const clienteLogado = <?php echo !empty($_SESSION['cliente_id']) ? 'true' : 'false'; ?>;

    
if (!clienteLogado) {
    mostrarNotificacao(
        "aviso",
        "Entre na sua conta",
        "Apenas usuários cadastrados podem realizar compras. Entre na sua conta ou cadastre-se.",
        [
            {
                texto: "Agora não",
                principal: false
            },
            {
                texto: "Fazer login",
                principal: true,
                acao: function() {
                    abrirPopuplogin();
                }
            }
        ]
    );
    return;
}


    const areaCarrinho = document.querySelector(
        ".produtos_categorias_carrinho"
    );

    if (areaCarrinho) {
        areaCarrinho.classList.remove("carrinho_aberto");
    }

    const popup = document.getElementById("modalFundoFimCompra");

    if (popup) {
        popup.style.display = "flex";
    }
}


            // =====================================================
            // PESQUISA DE PRODUTOS
            // =====================================================
            const barraPesquisa = document.getElementById("pesquisa");

            barraPesquisa.addEventListener("input", function () {
                const texto = this.value
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "");
                const produtos = document.querySelectorAll(".produto");
                produtos.forEach(function(produto) {
                    const nome = produto
                        .getAttribute("data-nome")
                        .toLowerCase()
                        .normalize("NFD")
                        .replace(/[\u0300-\u036f]/g, "");
                    if (nome.includes(texto)) {
                        produto.style.display = "";
                    } else {
                        produto.style.display = "none";
                    }
                });
            });

            // =====================================================
            // CONTADOR DO CARRINHO
            // =====================================================
            function atualizarContadorCarrinho() {

            const contador = document.getElementById("contador_carrinho");

            if (!contador) {
                return;
            }

            const quantidade = parseInt(contador.textContent);

            if (quantidade <= 0) {
                contador.style.display = "none";
            } else {
                contador.style.display = "flex";
            }
            }
            // =====================================================
            // MASCARA DO CPF
            // =====================================================
            document.addEventListener("DOMContentLoaded", function() {
                atualizarContadorCarrinho();
                });
            function mascaraCPF(input) {
                let valor = input.value.replace(/\D/g, '');

                valor = valor.substring(0, 11);

                valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
                valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

                input.value = valor;
            }

     
function mostrarNotificacao(tipo, titulo, mensagem, botoes) {
    const modal = document.getElementById("modalNotificacao");
    const icone = document.getElementById("iconeNotificacao");
    const tituloElemento = document.getElementById("tituloNotificacao");
    const texto = document.getElementById("textoNotificacao");
    const areaBotoes = document.getElementById("botoesNotificacao");

    const estilos = {
        sucesso: { classe: "icone-sucesso", simbolo: "✓" },
        erro:    { classe: "icone-erro", simbolo: "×" },
        aviso:   { classe: "icone-aviso", simbolo: "!" },
        info:    { classe: "icone-info", simbolo: "i" }
    };

    const estilo = estilos[tipo] || estilos.info;

    icone.className = "icone-notificacao " + estilo.classe;
    icone.textContent = estilo.simbolo;

    tituloElemento.textContent = titulo;
    texto.textContent = mensagem;
    areaBotoes.replaceChildren();

    botoes.forEach(function(botao) {
        const elemento = document.createElement("button");

        elemento.type = "button";
        elemento.className =
            "botao-notificacao " +
            (botao.principal
                ? "botao-principal"
                : "botao-secundario");

        elemento.textContent = botao.texto;

        elemento.addEventListener("click", function() {
            modal.style.display = "none";

            if (botao.acao) {
                botao.acao();
            }
        });

        areaBotoes.appendChild(elemento);
    });

    modal.style.display = "flex";

    const primeiroBotao = areaBotoes.querySelector("button");
    if (primeiroBotao) {
        primeiroBotao.focus();
    }
}

function fecharNotificacao() {
    document.getElementById("modalNotificacao").style.display = "none";
}

// Fechar clicando fora da caixa.
document.getElementById("modalNotificacao")
    .addEventListener("click", function(event) {
        if (event.target === this) {
            fecharNotificacao();
        }
    });

// Fechar com a tecla Escape.
document.addEventListener("keydown", function(event) {
    const modal = document.getElementById("modalNotificacao");

    if (event.key === "Escape" && modal.style.display === "flex") {
        fecharNotificacao();
    }
});
               

function confirmarLogout() {
    mostrarNotificacao(
        "aviso",
        "Sair da sua conta?",
        "Tem certeza de que deseja sair do Container do Queijo?",
        [
            {
                texto: "Continuar conectado",
                principal: false
            },
            {
                texto: "Sim, sair",
                principal: true,
                acao: function() {
                    window.location.href = "logout.php";
                }
            }
        ]
    );
}



            // =====================================================
            // MASCARA DO CPF
            // =====================================================
            function mascaraTelefone(input) {
                let valor = input.value.replace(/\D/g, '');

                valor = valor.substring(0, 11);

                if (valor.length <= 10) {
                    valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
                    valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
                } else {
                    valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
                    valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
                }

                input.value = valor;
            }

            <?php if (!empty($_SESSION['abrir_login'])): ?>
        abrirPopuplogin();
        <?php unset($_SESSION['abrir_login']); ?>
    <?php endif; ?>

    
const formaPagamentoBase = document.getElementById("form_pag_base");
const opcoesCartao = document.getElementById("opcoesCartao");
const tipoCartao = document.getElementById("tipoCartao");
const formaPagamentoFinal = document.getElementById("form_pag");

function atualizarFormaPagamento() {
    const selecionouCartao = formaPagamentoBase.value === "Cartão";

    opcoesCartao.style.display = selecionouCartao ? "block" : "none";
    tipoCartao.required = selecionouCartao;

    if (selecionouCartao) {
        formaPagamentoFinal.value = tipoCartao.value;
    } else {
        tipoCartao.value = "";
        formaPagamentoFinal.value = formaPagamentoBase.value;
    }
}

formaPagamentoBase.addEventListener("change", atualizarFormaPagamento);
tipoCartao.addEventListener("change", atualizarFormaPagamento);

// Garantir que o valor correto seja enviado ao formulário.
document.querySelector(
    '#modalFundoFimCompra form[action="finalizar_compra.php"]'
).addEventListener("submit", function(event) {
    atualizarFormaPagamento();

    if (!formaPagamentoFinal.value) {
    event.preventDefault();

    mostrarNotificacao(
        "aviso",
        "Forma de pagamento",
        "Selecione uma forma de pagamento para continuar.",
        [
            {
                texto: "Entendi",
                principal: true
            }
        ]
    );

    return;
}

    if (
    formaPagamentoBase.value === "Cartão" &&
    !tipoCartao.value
) {
    event.preventDefault();

    mostrarNotificacao(
        "aviso",
        "Escolha o tipo de cartão",
        "Selecione Débito ou Crédito para continuar.",
        [
            {
                texto: "Entendi",
                principal: true
            }
        ]
    );
}
}); 


const notificacaoCompra = <?php
    echo json_encode(
        $notificacaoCompra,
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );
?>;

if (notificacaoCompra) {
    if (notificacaoCompra.tipo === "sucesso") {
        mostrarNotificacao(
            "sucesso",
            notificacaoCompra.titulo,
            notificacaoCompra.mensagem,
            [
                {
                    texto: "Continuar",
                    principal: true
                }
            ]
        );
    }
}







(function () {
    const chave = 'posicaoRolagem';
    const params = new URLSearchParams(window.location.search);
    const restaurar = params.get('restaurar_rolagem') === '1';

    window.addEventListener('scroll', function () {
        sessionStorage.setItem(chave, String(window.scrollY));
    }, { passive: true });

    if (restaurar) {
        const valorSalvo = sessionStorage.getItem(chave);
        const posicao = valorSalvo === null ? 0 : Number(valorSalvo);

        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        function aplicarRolagem() {
            window.scrollTo(0, posicao);
        }

        window.addEventListener('load', function () {
            aplicarRolagem();

            requestAnimationFrame(function () {
                aplicarRolagem();
                setTimeout(aplicarRolagem, 100);
                setTimeout(aplicarRolagem, 300);
            });
        }, { once: true });

        if (document.readyState === 'complete') {
            aplicarRolagem();
            requestAnimationFrame(aplicarRolagem);
        }

        params.delete('restaurar_rolagem');

        history.replaceState(
            null,
            '',
            window.location.pathname +
            (params.toString() ? '?' + params.toString() : '') +
            window.location.hash
        );
    }
})();













// =====================================================
// ATUALIZAR VISUALMENTE O CARRINHO SEM RECARREGAR
// =====================================================

function escaparHTML(valor) {
    return String(valor ?? '').replace(/[&<>"']/g, function(caractere) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[caractere];
    });
}

function formatarMoeda(valor) {
    return Number(valor).toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

async function atualizarCarrinhoVisual() {
    try {
        const resposta = await fetch('obter_carrinho.php', {
            cache: 'no-store'
        });

        if (!resposta.ok) {
            throw new Error('Não foi possível consultar o carrinho.');
        }

        const dados = await resposta.json();

        if (!dados.sucesso) {
            throw new Error('Não foi possível atualizar o carrinho.');
        }

        const itens = dados.itens;
        const total = Number(dados.total);
        const lista = document.getElementById('lista-carrinho-dinamica');
        const resumo = document.getElementById('resumo-produtos-dinamico');
        const totalFinal = document.getElementById('total-final-dinamico');
        const contador = document.getElementById('contador_carrinho');
        const resumoCompra = document.getElementById('resumo-carrinho-dinamico');
        const carrinhoFinal = document.getElementById('carrinhoFinal');

        // Atualizar contador do topo
        if (contador) {
            contador.textContent = dados.quantidade;
            contador.style.display =
                dados.quantidade > 0 ? 'flex' : 'none';
        }

        // Atualizar lista do carrinho lateral
        if (lista) {
            let html = '<h2 align="center">Carrinho:</h2>';

            if (itens.length === 0) {
                html += '<div align="center">Carrinho vazio.</div>';
            } else {
                itens.forEach(function(item) {
                    html += `
                        <div style="padding:10px; margin:10px; border-bottom:1px solid #ccc;" align="center">
                            <strong>${escaparHTML(item.nome)}</strong>
                            <br>
                            Quantidade:
                            <button type="button" onclick="tirar1(${Number(item.id)})">-</button>
                            <strong>${Number(item.quantidade)}</strong>
                            <button type="button" onclick="adicionar1(${Number(item.id)})">+</button>
                            <br>
                            Preço: R$ ${formatarMoeda(item.subtotal)}
                            <br><br>
                            <button type="button" onclick="apagaProduto(${Number(item.id)})">
                                Retirar do carrinho
                            </button>
                        </div>
                    `;
                });

                html += `
                    <div align="center">
                        <button type="button" class="btn_carrinho" onclick="limparCarrinho()">
                            Limpar Carrinho
                        </button>
                    </div>
                `;
            }

            lista.innerHTML = html;
        }

        // Atualizar bloco "Fim da compra"
        if (resumoCompra) {
            if (itens.length === 0) {
                resumoCompra.innerHTML = `
                    <h2 align="center">Fim da compra</h2>
                    <div align="center">Carrinho vazio.</div>
                `;
            } else {
                resumoCompra.innerHTML = `
                    <h2 align="center">Fim da compra</h2>
                    <div align="center">
                        <h4>Total: R$ ${formatarMoeda(total)}</h4>
                        <button type="button"
                                onclick="abrirPopupFimCompra()"
                                class="btn_carrinho">
                            Comprar
                        </button>
                    </div>
                `;
            }
        }

        // Atualizar resumo dentro do popup de finalização
        if (resumo) {
            if (itens.length === 0) {
                resumo.innerHTML = '<p>Carrinho vazio.</p>';
            } else {
                resumo.innerHTML = itens.map(function(item) {
                    return `
                        <div class="item-resumo">
                            <img
                                src="../Produtos/${escaparHTML(item.imagem)}"
                                alt="${escaparHTML(item.nome)}"
                                class="imagem-resumo">

                            <div class="info-resumo">
                                <strong>${escaparHTML(item.nome)}</strong>
                                <span>Quantidade: ${Number(item.quantidade)}</span>
                                <span>Valor: R$ ${formatarMoeda(item.valor)}</span>
                                <strong>Subtotal: R$ ${formatarMoeda(item.subtotal)}</strong>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        // Atualizar total no popup
        if (totalFinal) {
            totalFinal.innerHTML = `
                <strong>Total da compra:</strong>
                <strong>R$ ${formatarMoeda(total)}</strong>
            `;
        }

        // Atualizar os produtos enviados ao finalizar a compra
        if (carrinhoFinal) {
            carrinhoFinal.value = JSON.stringify(
                itens.map(function(item) {
                    return {
                        idProduto: Number(item.id),
                        quantidade: Number(item.quantidade)
                    };
                })
            );
        }

    } catch (erro) {
        console.error('Erro ao atualizar o carrinho:', erro);
    }
}

// =====================================================
// ADICIONAR PRODUTO SEM RECARREGAR A PÁGINA
// =====================================================

document.querySelectorAll('.adicionar-ao-carrinho').forEach(function(botao) {
    botao.addEventListener('click', async function() {
        const idProduto = this.dataset.id;
        const botaoAtual = this;
        const textoOriginal = botaoAtual.textContent;

        botaoAtual.disabled = true;
        botaoAtual.textContent = 'Adicionando...';

        try {
            const dados = new URLSearchParams();
            dados.set('idProduto', idProduto);

            const resposta = await fetch('adicionar_carrinho.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: dados.toString()
            });

            const resultado = await resposta.json();

            if (!resultado.sucesso) {
                mostrarNotificacao(
                    'aviso',
                    'Não foi possível adicionar',
                    resultado.mensagem,
                    [{ texto: 'Entendi', principal: true }]
                );
                return;
            }

            // Atualizar todos os elementos do carrinho imediatamente
            await atualizarCarrinhoVisual();

            mostrarNotificacao(
                'sucesso',
                'Produto adicionado!',
                resultado.mensagem,
                [{ texto: 'Continuar comprando', principal: true }]
            );

        } catch (erro) {
            console.error(erro);

            mostrarNotificacao(
                'erro',
                'Erro ao adicionar',
                'Não foi possível adicionar o produto. Verifique sua conexão e tente novamente.',
                [{ texto: 'Entendi', principal: true }]
            );
        } finally {
            botaoAtual.disabled = false;
            botaoAtual.textContent = textoOriginal;
        }
    });
});

</script>
</body>
</html>