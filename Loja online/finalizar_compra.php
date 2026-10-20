
<?php

session_start();

require_once __DIR__ . "/conexao.php";

// ======================================================
// VERIFICAR MÉTODO
// ======================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Acesso inválido.");
}

// ======================================================
// IDENTIFICAR CLIENTE
// ======================================================

$idCliente = null;

if (!empty($_SESSION['cliente_id'])) {
    $idCliente = (int) $_SESSION['cliente_id'];
}

// ======================================================
// RECEBER DADOS DO PEDIDO
// ======================================================

$formaPagamento = trim($_POST['form_pag'] ?? '');
$carrinhoRecebido = $_POST['carrinho'] ?? '';

if (is_array($carrinhoRecebido)) {
    $itensCarrinho = $carrinhoRecebido;
} else {
    $itensCarrinho = json_decode($carrinhoRecebido, true);
}

// ======================================================
// VALIDAR FORMA DE PAGAMENTO
// ======================================================

// Valores aceitos pela coluna formaDePagamento do banco.
$formasPermitidas = [
    'Dinheiro',
    'Cartao',
    'Pix',
    'Crediario'
];

// Aceitar também os rótulos apresentados na interface.
$mapaPagamento = [
    'Dinheiro'  => 'Dinheiro',
    'Cartão'    => 'Cartao',
    'Cartao'    => 'Cartao',
    'Pix'       => 'Pix',
    'Crediário' => 'Crediario',
    'Crediario' => 'Crediario'
];

if (!isset($mapaPagamento[$formaPagamento])) {
    echo "<script>
        alert('Forma de pagamento inválida.');
        history.back();
    </script>";
    exit;
}

$formaPagamento = $mapaPagamento[$formaPagamento];

// ======================================================
// VALIDAR CARRINHO
// ======================================================

if (!is_array($itensCarrinho) || empty($itensCarrinho)) {
    echo "<script>
        alert('Seu carrinho está vazio ou é inválido.');
        history.back();
    </script>";
    exit;
}

// ======================================================
// INICIAR TRANSAÇÃO
// ======================================================

try {

    if (!$conexao->begin_transaction()) {
        throw new Exception(
            "Não foi possível iniciar a transação."
        );
    }

    // ==================================================
    // PREPARAR CONSULTA DO PRODUTO
    // ==================================================

    $sqlProduto = "
        SELECT idProduto, nome, valor, estoque
        FROM produtos
        WHERE idProduto = ?
        FOR UPDATE
    ";

    $stmtProduto = $conexao->prepare($sqlProduto);

    if (!$stmtProduto) {
        throw new Exception(
            "Erro ao preparar consulta do produto: " .
            $conexao->error
        );
    }

    // ==================================================
    // VALIDAR PRODUTOS E CALCULAR TOTAL
    // ==================================================

    $produtosPedido = [];
    $valorTotal = 0.00;

    foreach ($itensCarrinho as $item) {

        if (!is_array($item)) {
            throw new Exception(
                "Existe um item inválido no carrinho."
            );
        }

        $idProduto = (int) (
            $item['idProduto'] ?? $item['id'] ?? 0
        );

        $quantidade = (int) ($item['quantidade'] ?? 0);

        if ($idProduto <= 0 || $quantidade <= 0) {
            throw new Exception(
                "O produto ou a quantidade é inválido."
            );
        }

        $stmtProduto->bind_param("i", $idProduto);

        if (!$stmtProduto->execute()) {
            throw new Exception(
                "Erro ao consultar produto: " .
                $stmtProduto->error
            );
        }

        $resultadoProduto = $stmtProduto->get_result();
        $produto = $resultadoProduto->fetch_assoc();

        if (!$produto) {
            throw new Exception(
                "O produto ID " . $idProduto .
                " não foi encontrado."
            );
        }

        $estoqueAtual = (float) $produto['estoque'];
        $valorUnitario = (float) $produto['valor'];

        if ($estoqueAtual < $quantidade) {
            throw new Exception(
                "Estoque insuficiente para " .
                $produto['nome'] .
                ". Disponível: " . $estoqueAtual .
                "; solicitado: " . $quantidade . "."
            );
        }

        $subtotal = round(
            $valorUnitario * $quantidade,
            2
        );

        $produtosPedido[] = [
            'idProduto' => $idProduto,
            'nome' => $produto['nome'],
            'quantidade' => $quantidade,
            'valorUnitario' => $valorUnitario,
            'subtotal' => $subtotal
        ];

        $valorTotal += $subtotal;
    }

    $stmtProduto->close();

    if (empty($produtosPedido)) {
        throw new Exception(
            "Nenhum produto válido foi encontrado."
        );
    }

    $valorFinal = round($valorTotal, 2);

    if ($valorFinal <= 0) {
        throw new Exception(
            "O valor da compra deve ser maior que zero."
        );
    }

    // ==================================================
    // REGISTRAR VENDA
    // ==================================================

    if ($idCliente !== null) {

        $sqlVenda = "
            INSERT INTO vendas
                (idCliente, valor, data, formaDePagamento)
            VALUES (?, ?, CURDATE(), ?)
        ";

        $stmtVenda = $conexao->prepare($sqlVenda);

        if (!$stmtVenda) {
            throw new Exception(
                "Erro ao preparar venda: " .
                $conexao->error
            );
        }

        $stmtVenda->bind_param(
            "ids",
            $idCliente,
            $valorFinal,
            $formaPagamento
        );

    } else {

        $sqlVenda = "
            INSERT INTO vendas
                (idCliente, valor, data, formaDePagamento)
            VALUES (NULL, ?, CURDATE(), ?)
        ";

        $stmtVenda = $conexao->prepare($sqlVenda);

        if (!$stmtVenda) {
            throw new Exception(
                "Erro ao preparar venda: " .
                $conexao->error
            );
        }

        $stmtVenda->bind_param(
            "ds",
            $valorFinal,
            $formaPagamento
        );
    }

    if (!$stmtVenda->execute()) {
        throw new Exception(
            "Erro ao registrar venda: " .
            $stmtVenda->error
        );
    }

    $idVenda = $conexao->insert_id;
    $stmtVenda->close();

    // ==================================================
    // PREPARAR INSERÇÃO EM vendaprodutos
    // ==================================================
    // Estrutura confirmada:
    // idVendaProdutos, quantidade, idVenda, idProduto
    // ==================================================

    $sqlItem = "
        INSERT INTO vendaprodutos
            (idVenda, idProduto, quantidade)
        VALUES (?, ?, ?)
    ";

    $stmtItem = $conexao->prepare($sqlItem);

    if (!$stmtItem) {
        throw new Exception(
            "Erro ao preparar itens da venda: " .
            $conexao->error
        );
    }

    // ==================================================
    // PREPARAR BAIXA DE ESTOQUE
    // ==================================================

    $sqlEstoque = "
        UPDATE produtos
        SET estoque = estoque - ?
        WHERE idProduto = ?
          AND estoque >= ?
    ";

    $stmtEstoque = $conexao->prepare($sqlEstoque);

    if (!$stmtEstoque) {
        throw new Exception(
            "Erro ao preparar atualização do estoque: " .
            $conexao->error
        );
    }

    // ==================================================
    // REGISTRAR ITENS E BAIXAR ESTOQUE
    // ==================================================

    foreach ($produtosPedido as $produto) {

        $idProduto = (int) $produto['idProduto'];
        $quantidade = (int) $produto['quantidade'];

        // Registrar o item da venda.
        $stmtItem->bind_param(
            "iii",
            $idVenda,
            $idProduto,
            $quantidade
        );

        if (!$stmtItem->execute()) {
            throw new Exception(
                "Erro ao registrar o produto " .
                $produto['nome'] . ": " .
                $stmtItem->error
            );
        }

        // Baixar o estoque.
        $stmtEstoque->bind_param(
            "iii",
            $quantidade,
            $idProduto,
            $quantidade
        );

        if (!$stmtEstoque->execute()) {
            throw new Exception(
                "Erro ao atualizar estoque de " .
                $produto['nome'] . ": " .
                $stmtEstoque->error
            );
        }

        if ($stmtEstoque->affected_rows !== 1) {
            throw new Exception(
                "Não foi possível atualizar o estoque de " .
                $produto['nome'] . "."
            );
        }
    }

    $stmtItem->close();
    $stmtEstoque->close();

    // ==================================================
    // CONFIRMAR TODAS AS ALTERAÇÕES
    // ==================================================

    if (!$conexao->commit()) {
        throw new Exception(
            "Não foi possível confirmar a compra no banco."
        );
    }

    // Limpar o carrinho somente após o sucesso.
    $_SESSION['carrinho'] = [];

    // ==================================================
    // MOSTRAR CONFIRMAÇÃO
    // ==================================================

    $valorFormatado = number_format(
        $valorFinal,
        2,
        ',',
        '.'
    );

    $mensagem = "Compra finalizada com sucesso!\n\n" .
                "Número da venda: " . $idVenda . "\n" .
                "Valor: R$ " . $valorFormatado;

    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Compra finalizada</title>
    </head>
    <body>
        <script>
            alert(" .
            json_encode(
                $mensagem,
                JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
            ) .
            ");
            window.location.href = 'inicio.php';
        </script>
    </body>
    </html>";

} catch (Throwable $e) {

    // Desfazer as alterações se a transação ainda estiver ativa.
    $conexao->rollback();

    error_log(
        "Erro ao finalizar compra: " . $e->getMessage()
    );

    $mensagemErro =
        "Não foi possível finalizar a compra.\n\n" .
        $e->getMessage();

    echo "<!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro na compra</title>
    </head>
    <body>
        <script>
            alert(" .
            json_encode(
                $mensagemErro,
                JSON_UNESCAPED_UNICODE |
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
            ) .
            ");
            history.back();
        </script>
    </body>
    </html>";

} finally {

    $conexao->close();
}
?>
