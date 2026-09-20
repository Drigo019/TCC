<?php

session_start();

require_once __DIR__ . "/conexao.php";

// ======================================================
// VERIFICAÇÃO DO MÉTODO
// ======================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acesso inválido.");
}

// ======================================================
// IDENTIFICAR CLIENTE
// ======================================================
//
// Se estiver logado, pega o ID da sessão.
// Se não estiver logado, fica NULL.
//
// IMPORTANTE:
// Ajuste os nomes das sessões abaixo caso no seu
// sistema de login você use outro nome.
//

$idCliente = null;

if (isset($_SESSION['idCliente']) && !empty($_SESSION['idCliente'])) {

    $idCliente = (int) $_SESSION['idCliente'];

} elseif (isset($_SESSION['id_cliente']) && !empty($_SESSION['id_cliente'])) {

    $idCliente = (int) $_SESSION['id_cliente'];

} elseif (isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id'])) {

    $idCliente = (int) $_SESSION['usuario_id'];
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
// INICIAR TRANSAÇÃO
// ======================================================

$conexao->begin_transaction();

try {

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
            "Erro ao preparar consulta do produto: " . $conexao->error
        );
    }

    // ==================================================
    // PREPARAR ATUALIZAÇÃO DO ESTOQUE
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
            "Erro ao preparar atualização do estoque: " . $conexao->error
        );
    }

    // ==================================================
    // PRIMEIRA PASSADA
    // VALIDAR PRODUTOS E CALCULAR TOTAL
    // ==================================================

    $produtosPedido = [];

    $valorTotal = 0.00;

    foreach ($itensCarrinho as $item) {

        // ----------------------------------------------
        // ACEITA idProduto OU id
        // ----------------------------------------------

        $idProduto = (int) (
            $item['idProduto']
            ?? $item['id']
            ?? 0
        );

        $quantidade = (int) (
            $item['quantidade']
            ?? 0
        );

        // ----------------------------------------------
        // VALIDAR ID
        // ----------------------------------------------

        if ($idProduto <= 0) {

            throw new Exception(
                "Existe um produto inválido no carrinho."
            );
        }

        // ----------------------------------------------
        // VALIDAR QUANTIDADE
        // ----------------------------------------------

        if ($quantidade <= 0) {

            throw new Exception(
                "A quantidade de um produto é inválida."
            );
        }

        // ----------------------------------------------
        // BUSCAR PRODUTO
        // ----------------------------------------------

        $stmtProduto->bind_param(
            "i",
            $idProduto
        );

        if (!$stmtProduto->execute()) {

            throw new Exception(
                "Erro ao consultar produto: " .
                $stmtProduto->error
            );
        }

        $resultadoProduto = $stmtProduto->get_result();

        if ($resultadoProduto->num_rows === 0) {

            throw new Exception(
                "O produto ID " .
                $idProduto .
                " não foi encontrado."
            );
        }

        $produto = $resultadoProduto->fetch_assoc();

        // ----------------------------------------------
        // DADOS DO PRODUTO
        // ----------------------------------------------

        $estoqueAtual = (int) $produto['estoque'];

        $valorUnitario = (float) $produto['valor'];

        // ----------------------------------------------
        // VERIFICAR ESTOQUE
        // ----------------------------------------------

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

        // ----------------------------------------------
        // CALCULAR SUBTOTAL
        // ----------------------------------------------

        $subtotal = $valorUnitario * $quantidade;

        // ----------------------------------------------
        // GUARDAR PRODUTO
        // ----------------------------------------------

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

    // ==================================================
    // VERIFICAR PRODUTOS
    // ==================================================

    if (empty($produtosPedido)) {

        throw new Exception(
            "Nenhum produto válido foi encontrado."
        );
    }

    // ==================================================
    // ARREDONDAR TOTAL
    // ==================================================

    $valorTotal = round($valorTotal, 2);

    // ==================================================
    // VALOR FINAL
    // ==================================================

    $valorFinal = $valorTotal;

    if ($valorFinal < 0) {

        throw new Exception(
            "O valor final da compra não pode ser negativo."
        );
    }

    $valorFinal = round($valorFinal, 2);

    // ==================================================
    // CADASTRAR VENDA
    // ==================================================
    //
    // AQUI ESTÁ A PRINCIPAL ALTERAÇÃO:
    //
    // Cliente logado:
    //     idCliente = ID do cliente
    //
    // Cliente não logado:
    //     idCliente = NULL
    //
    // ==================================================

    if ($idCliente !== null) {

    // ==============================================
    // CLIENTE LOGADO
    // ==============================================

    $sqlVenda = "
        INSERT INTO vendas
        (
            idCliente,
            valor,
            data,
            formaDePagamento
        )
        VALUES (?, ?, CURDATE(), ?)
    ";

    $stmtVenda = $conexao->prepare($sqlVenda);

    if (!$stmtVenda) {
        throw new Exception(
            "Erro ao preparar cadastro da venda: " .
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

    // ==============================================
    // CLIENTE NÃO LOGADO
    // ==============================================

    $sqlVenda = "
        INSERT INTO vendas
        (
            idCliente,
            valor,
            data,
            formaDePagamento
        )
        VALUES (NULL, ?, CURDATE(), ?)
    ";

    $stmtVenda = $conexao->prepare($sqlVenda);

    if (!$stmtVenda) {
        throw new Exception(
            "Erro ao preparar cadastro da venda: " .
            $conexao->error
        );
    }

    $stmtVenda->bind_param(
        "ds",
        $valorFinal,
        $formaPagamento
    );
}

    // ==================================================
    // EXECUTAR VENDA
    // ==================================================

    if (!$stmtVenda->execute()) {

        throw new Exception(
            "Erro ao cadastrar venda: " .
            $stmtVenda->error
        );
    }

    // ==================================================
    // PEGAR ID DA VENDA
    // ==================================================

    $idVenda = $conexao->insert_id;

    $stmtVenda->close();

    // ==================================================
    // PREPARAR CADASTRO DOS ITENS
    // ==================================================

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

    $stmtItem = $conexao->prepare($sqlItem);

    if (!$stmtItem) {

        throw new Exception(
            "Erro ao preparar itens da venda: " .
            $conexao->error
        );
    }

    // ==================================================
    // CADASTRAR ITENS E BAIXAR ESTOQUE
    // ==================================================

    foreach ($produtosPedido as $produto) {

        $idProduto = $produto['idProduto'];

        $quantidade = $produto['quantidade'];

        $valorUnitario = $produto['valorUnitario'];

        // ----------------------------------------------
        // CADASTRAR ITEM
        // ----------------------------------------------

        $stmtItem->bind_param(
            "iiid",
            $idVenda,
            $idProduto,
            $quantidade,
            $valorUnitario
        );

        if (!$stmtItem->execute()) {

            throw new Exception(
                "Erro ao cadastrar item da venda: " .
                $stmtItem->error
            );
        }

        // ----------------------------------------------
        // BAIXAR ESTOQUE
        // ----------------------------------------------

        $stmtEstoque->bind_param(
            "iii",
            $quantidade,
            $idProduto,
            $quantidade
        );

        if (!$stmtEstoque->execute()) {

            throw new Exception(
                "Erro ao atualizar estoque: " .
                $stmtEstoque->error
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

    // ==================================================
    // CONFIRMAR TRANSAÇÃO
    // ==================================================

    $conexao->commit();

    // ==================================================
    // LIMPAR CARRINHO
    // ==================================================

    $_SESSION['carrinho'] = [];

    // ==================================================
    // MENSAGEM DE SUCESSO
    // ==================================================

    $valorFormatado = number_format(
        $valorFinal,
        2,
        ',',
        '.'
    );

    echo "
    <!DOCTYPE html>

    <html lang='pt-BR'>

    <head>

        <meta charset='UTF-8'>

        <title>Compra finalizada</title>

    </head>

    <body>

        <script>

            alert(
                'Compra finalizada com sucesso!\\n\\n' +
                'Número da venda: {$idVenda}\\n' +
                'Valor: R$ {$valorFormatado}'
            );

            window.location.href = 'inicio.php';

        </script>

    </body>

    </html>
    ";

} catch (Throwable $e) {

    // ==================================================
    // DESFAZER TUDO
    // ==================================================

    $conexao->rollback();

    // ==================================================
    // MOSTRAR ERRO
    // ==================================================

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
                "Não foi possível finalizar a compra.\n\n" .
                $e->getMessage(),
                JSON_UNESCAPED_UNICODE
            ) .
            ");

            history.back();

        </script>

    </body>

    </html>
    ";
}

$conexao->close();

?>
