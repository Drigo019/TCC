<?php

session_start();

require('conexao.php');

/*
=========================================================
FINALIZAR COMPRA
Banco: containerdoqueijo

Tabelas utilizadas:
- clientes
- produtos
- vendas
- itens_venda

IMPORTANTE:
O idCliente deve estar em:
1. $_SESSION['idCliente']; ou
2. POST['idCliente']
=========================================================
*/

// ======================================================
// VERIFICAÇÃO DO MÉTODO
// ======================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acesso inválido.");
}

// ======================================================
// PEGAR ID DO CLIENTE
// ======================================================

$idCliente = (int)($_POST['idCliente'] ?? $_SESSION['idCliente'] ?? 0);

if ($idCliente <= 0) {
    exit("
        <script>
            alert('Cliente não identificado. Faça login antes de finalizar a compra.');
            history.back();
        </script>
    ");
}

// ======================================================
// PEGAR DADOS DO PEDIDO
// ======================================================

$formaPagamento = trim($_POST['form_pag'] ?? '');
$carrinhoRecebido = $_POST['carrinho'] ?? '';

// Aceita carrinho enviado como JSON.
// Também aceita caso o campo já venha como array.
if (is_array($carrinhoRecebido)) {
    $itensCarrinho = $carrinhoRecebido;
} else {
    $itensCarrinho = json_decode($carrinhoRecebido, true);
}

if (!is_array($itensCarrinho) || empty($itensCarrinho)) {
    exit("
        <script>
            alert('O carrinho está vazio.');
            history.back();
        </script>
    ");
}

// ======================================================
// VALIDAR FORMA DE PAGAMENTO
// ======================================================

$formasPermitidas = [
    'Dinheiro',
    'Cartão',
    'Pix',
    'Crediário'
];

if (!in_array($formaPagamento, $formasPermitidas, true)) {
    exit("
        <script>
            alert('Forma de pagamento inválida.');
            history.back();
        </script>
    ");
}

// ======================================================
// GARANTIR QUE O CLIENTE EXISTE
// ======================================================

$sqlCliente = "SELECT idCliente FROM clientes WHERE idCliente = ? LIMIT 1";

$stmtCliente = $conn->prepare($sqlCliente);

if (!$stmtCliente) {
    exit("Erro ao verificar cliente: " . $conn->error);
}

$stmtCliente->bind_param("i", $idCliente);
$stmtCliente->execute();

$resultadoCliente = $stmtCliente->get_result();

if ($resultadoCliente->num_rows === 0) {
    $stmtCliente->close();

    exit("
        <script>
            alert('Cliente não encontrado.');
            history.back();
        </script>
    ");
}

$stmtCliente->close();

// ======================================================
// INICIAR TRANSAÇÃO
// ======================================================

$conn->begin_transaction();

try {

    /*
    =====================================================
    PREPARAR CONSULTA DO PRODUTO

    O preço é buscado novamente no banco.
    Assim o valor do pedido não depende do valor enviado
    pelo navegador.
    =====================================================
    */

    $sqlProduto = "
        SELECT idProduto, nome, valor, estoque
        FROM produtos
        WHERE idProduto = ?
        FOR UPDATE
    ";

    $stmtProduto = $conn->prepare($sqlProduto);

    if (!$stmtProduto) {
        throw new Exception(
            "Erro ao preparar consulta do produto: " . $conn->error
        );
    }

    /*
    =====================================================
    PREPARAR ATUALIZAÇÃO DO ESTOQUE
    =====================================================
    */

    $sqlEstoque = "
        UPDATE produtos
        SET estoque = estoque - ?
        WHERE idProduto = ?
          AND estoque >= ?
    ";

    $stmtEstoque = $conn->prepare($sqlEstoque);

    if (!$stmtEstoque) {
        throw new Exception(
            "Erro ao preparar atualização do estoque: " . $conn->error
        );
    }

    /*
    =====================================================
    PRIMEIRA PASSADA:
    VALIDAR TODOS OS PRODUTOS E CALCULAR O TOTAL
    =====================================================
    */

    $produtosPedido = [];
    $valorTotal = 0.00;

    foreach ($itensCarrinho as $item) {

        /*
        Aceita:
        idProduto
        ou
        id
        */

        $idProduto = (int)($item['idProduto'] ?? $item['id'] ?? 0);

        $quantidade = (int)($item['quantidade'] ?? 0);

        if ($idProduto <= 0) {
            throw new Exception("Existe um produto inválido no carrinho.");
        }

        if ($quantidade <= 0) {
            throw new Exception("A quantidade de um produto é inválida.");
        }

        // Busca produto e trava a linha durante a transação.
        $stmtProduto->bind_param("i", $idProduto);

        if (!$stmtProduto->execute()) {
            throw new Exception(
                "Erro ao consultar produto: " . $stmtProduto->error
            );
        }

        $resultadoProduto = $stmtProduto->get_result();

        if ($resultadoProduto->num_rows === 0) {
            throw new Exception(
                "O produto ID " . $idProduto . " não foi encontrado."
            );
        }

        $produto = $resultadoProduto->fetch_assoc();

        $estoqueAtual = (int)$produto['estoque'];
        $valorUnitario = (float)$produto['valor'];

        if ($estoqueAtual < $quantidade) {
            throw new Exception(
                "Estoque insuficiente para o produto: " .
                $produto['nome'] .
                ". Disponível: " .
                $estoqueAtual .
                ". Solicitado: " .
                $quantidade .
                "."
            );
        }

        $subtotal = $valorUnitario * $quantidade;

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
        throw new Exception("Nenhum produto válido foi encontrado.");
    }

    // Arredondamento do valor final.
    $valorTotal = round($valorTotal, 2);

    /*
    =====================================================
    DESCONTO E ACRÉSCIMO
    =====================================================
    */

    $desconto = (float)($_POST['desconto'] ?? 0);
    $acrescimo = (float)($_POST['acrescimo'] ?? $_POST['acrecimo'] ?? 0);

    if ($desconto < 0) {
        $desconto = 0;
    }

    if ($acrescimo < 0) {
        $acrescimo = 0;
    }

    $valorFinal = $valorTotal - $desconto + $acrescimo;

    if ($valorFinal < 0) {
        throw new Exception("O valor final da compra não pode ser negativo.");
    }

    $valorFinal = round($valorFinal, 2);

    /*
    =====================================================
    CADASTRAR A VENDA
    =====================================================
    */

    $sqlVenda = "
        INSERT INTO vendas
        (
            idCliente,
            valor,
            data,
            formaDePagamento,
            desconto,
            acrecimo
        )
        VALUES (?, ?, CURDATE(), ?, ?, ?)
    ";

    $stmtVenda = $conn->prepare($sqlVenda);

    if (!$stmtVenda) {
        throw new Exception(
            "Erro ao preparar cadastro da venda: " . $conn->error
        );
    }

    $stmtVenda->bind_param(
        "idsdd",
        $idCliente,
        $valorFinal,
        $formaPagamento,
        $desconto,
        $acrescimo
    );

    if (!$stmtVenda->execute()) {
        throw new Exception(
            "Erro ao cadastrar venda: " . $stmtVenda->error
        );
    }

    // Guarda o ID da venda criada.
    $idVenda = $conn->insert_id;

    $stmtVenda->close();

    /*
    =====================================================
    PREPARAR CADASTRO DOS ITENS
    =====================================================
    */

    $sqlItem = "
        INSERT INTO itens_venda
        (
            idVenda,
            idProduto,
            quantidade,
            valorUnitario
        )
        VALUES (?, ?, ?, ?)
    ";

    $stmtItem = $conn->prepare($sqlItem);

    if (!$stmtItem) {
        throw new Exception(
            "Erro ao preparar itens da venda: " . $conn->error
        );
    }

    /*
    =====================================================
    CADASTRAR ITENS E BAIXAR ESTOQUE
    =====================================================
    */

    foreach ($produtosPedido as $produto) {

        $idProduto = $produto['idProduto'];
        $quantidade = $produto['quantidade'];
        $valorUnitario = $produto['valorUnitario'];

        // Cadastra o item.
        $stmtItem->bind_param(
            "iiid",
            $idVenda,
            $idProduto,
            $quantidade,
            $valorUnitario
        );

        if (!$stmtItem->execute()) {
            throw new Exception(
                "Erro ao cadastrar item da venda: " . $stmtItem->error
            );
        }

        // Baixa o estoque.
        $stmtEstoque->bind_param(
            "iii",
            $quantidade,
            $idProduto,
            $quantidade
        );

        if (!$stmtEstoque->execute()) {
            throw new Exception(
                "Erro ao atualizar estoque: " . $stmtEstoque->error
            );
        }

        if ($stmtEstoque->affected_rows !== 1) {
            throw new Exception(
                "Não foi possível atualizar o estoque do produto: " .
                $produto['nome']
            );
        }
    }

    $stmtItem->close();
    $stmtEstoque->close();

    /*
    =====================================================
    CONFIRMAR TUDO
    =====================================================
    */

    $conn->commit();

    // Limpar carrinho da sessão.
    $_SESSION['carrinho'] = [];

    /*
    =====================================================
    MENSAGEM DE SUCESSO
    =====================================================
    */

    echo "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Compra finalizada</title>
    </head>
    <body>

    <script>
        alert('Compra finalizada com sucesso!\\nNúmero da venda: {$idVenda}\\nValor: R$ " .
        number_format($valorFinal, 2, ',', '.') .
        "');
        window.location.href = 'inicio.php';
    </script>

    </body>
    </html>
    ";

} catch (Throwable $e) {

    /*
    =====================================================
    SE QUALQUER COISA DER ERRADO:
    DESFAZ A VENDA, ITENS E ESTOQUE.
    =====================================================
    */

    $conn->rollback();

    echo "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>
    </head>
    <body>

    <script>
        alert(" .
        json_encode(
            "Não foi possível finalizar a compra.\n\n" . $e->getMessage(),
            JSON_UNESCAPED_UNICODE
        ) .
        ");
        history.back();
    </script>

    </body>
    </html>
    ";
}

$conn->close();

?>
