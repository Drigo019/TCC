
<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');

function responder($sucesso, $mensagem, $dados = []) {
    echo json_encode(
        array_merge([
            'sucesso' => $sucesso,
            'mensagem' => $mensagem
        ], $dados),
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// =====================================================
// RECEBER E VALIDAR OS DADOS
// =====================================================

$dados = json_decode(file_get_contents('php://input'), true);

if (!is_array($dados)) {
    responder(false, 'Não foi possível receber os dados da venda.');
}

$produtos = $dados['produtos'] ?? [];
$valorInformado = (float) ($dados['valor'] ?? 0);
$formaRecebida = trim($dados['formaDePagamento'] ?? '');

$mapaPagamento = [
    'dinheiro' => 'Dinheiro',
    'debito' => 'Debito',
    'crédito' => 'Credito',
    'credito' => 'Credito',
    'pix' => 'Pix',
    'crediario' => 'Crediario'
];

$chavePagamento = strtolower($formaRecebida);

if (!isset($mapaPagamento[$chavePagamento])) {
    responder(false, 'Forma de pagamento inválida.');
}

$formaPagamento = $mapaPagamento[$chavePagamento];

if (!is_array($produtos) || empty($produtos)) {
    responder(false, 'Nenhum produto foi informado.');
}

if ($valorInformado <= 0) {
    responder(false, 'O valor da venda deve ser maior que zero.');
}

// =====================================================
// DADOS ESPECÍFICOS DO CREDIÁRIO
// =====================================================

$nomeCliente = null;
$dataVencimento = null;
$statusCrediario = null;

if ($formaPagamento === 'Crediario') {

    $nomeCliente = trim($dados['nomeCliente'] ?? '');
    $dataVencimento = trim($dados['dataVencimento'] ?? '');

    if ($nomeCliente === '') {
        responder(false, 'Informe o nome do cliente para o crediário.');
    }

    if (mb_strlen($nomeCliente, 'UTF-8') > 150) {
        responder(false, 'O nome do cliente deve ter no máximo 150 caracteres.');
    }

    $dataValida = DateTime::createFromFormat(
        '!Y-m-d',
        $dataVencimento
    );

    if (
        !$dataValida ||
        $dataValida->format('Y-m-d') !== $dataVencimento
    ) {
        responder(false, 'Informe uma data de vencimento válida.');
    }

    if ($dataVencimento < date('Y-m-d')) {
        responder(false, 'A data de vencimento não pode estar no passado.');
    }

    $statusCrediario = 'Pendente';
}

// =====================================================
// INICIAR TRANSAÇÃO
// =====================================================

if (!$conn->begin_transaction()) {
    responder(false, 'Não foi possível iniciar a transação.');
}

try {

    // -------------------------------------------------
    // VALIDAR ESTOQUE E CALCULAR O VALOR REAL
    // -------------------------------------------------

    $stmtProduto = $conn->prepare(
        'SELECT nome, valor, estoque
         FROM produtos
         WHERE idProduto = ?
         FOR UPDATE'
    );

    if (!$stmtProduto) {
        throw new Exception('Erro ao preparar consulta dos produtos.');
    }

    $produtosValidados = [];
    $valorTotal = 0;

    foreach ($produtos as $produto) {

        $idProduto = (int) ($produto['id'] ?? $produto['idProduto'] ?? 0);
        $quantidade = (int) ($produto['quantidade'] ?? 0);

        if ($idProduto <= 0 || $quantidade <= 0) {
            throw new Exception('Produto ou quantidade inválida.');
        }

        $stmtProduto->bind_param('i', $idProduto);

        if (!$stmtProduto->execute()) {
            throw new Exception('Erro ao consultar o estoque.');
        }

        $resultado = $stmtProduto->get_result();
        $dadosProduto = $resultado->fetch_assoc();

        if (!$dadosProduto) {
            throw new Exception(
                'O produto de código interno ' . $idProduto . ' não foi encontrado.'
            );
        }

        if ((int) $dadosProduto['estoque'] < $quantidade) {
            throw new Exception(
                'Estoque insuficiente para ' . $dadosProduto['nome'] .
                '. Disponível: ' . $dadosProduto['estoque'] . '.'
            );
        }

        $subtotal = round(
            (float) $dadosProduto['valor'] * $quantidade,
            2
        );

        $valorTotal += $subtotal;

        $produtosValidados[] = [
            'id' => $idProduto,
            'nome' => $dadosProduto['nome'],
            'quantidade' => $quantidade
        ];
    }

    $stmtProduto->close();

    $valorTotal = round($valorTotal, 2);

    if ($valorTotal <= 0) {
        throw new Exception('O valor calculado da venda é inválido.');
    }

    // Evita registrar um valor diferente daquele calculado
    // a partir dos preços atuais do banco.
    if (abs($valorTotal - $valorInformado) > 0.02) {
        throw new Exception(
            'O valor do carrinho mudou. Confira os produtos e tente novamente.'
        );
    }

    // -------------------------------------------------
    // REGISTRAR VENDA E DADOS DO CREDIÁRIO
    // -------------------------------------------------

    $sqlVenda = '
        INSERT INTO vendas
            (
                valor,
                data,
                formaDePagamento,
                nomeCliente,
                dataVencimento,
                statusCrediario,
                dataPagamento
            )
        VALUES (?, CURDATE(), ?, ?, ?, ?, NULL)
    ';

    $stmtVenda = $conn->prepare($sqlVenda);

    if (!$stmtVenda) {
        throw new Exception('Erro ao preparar o registro da venda.');
    }

    $stmtVenda->bind_param(
        'dssss',
        $valorTotal,
        $formaPagamento,
        $nomeCliente,
        $dataVencimento,
        $statusCrediario
    );

    if (!$stmtVenda->execute()) {
        throw new Exception('Erro ao registrar a venda: ' . $stmtVenda->error);
    }

    $idVenda = $conn->insert_id;
    $stmtVenda->close();

    // -------------------------------------------------
    // BAIXAR ESTOQUE
    // -------------------------------------------------

    $stmtEstoque = $conn->prepare(
        'UPDATE produtos
         SET estoque = estoque - ?
         WHERE idProduto = ?
           AND estoque >= ?'
    );

    if (!$stmtEstoque) {
        throw new Exception('Erro ao preparar a baixa do estoque.');
    }

    foreach ($produtosValidados as $produto) {

        $idProduto = $produto['id'];
        $quantidade = $produto['quantidade'];

        $stmtEstoque->bind_param(
            'iii',
            $quantidade,
            $idProduto,
            $quantidade
        );

        if (!$stmtEstoque->execute()) {
            throw new Exception(
                'Erro ao atualizar o estoque de ' . $produto['nome'] . '.'
            );
        }

        if ($stmtEstoque->affected_rows !== 1) {
            throw new Exception(
                'Não foi possível atualizar o estoque de ' . $produto['nome'] . '.'
            );
        }
    }

    $stmtEstoque->close();

    // -------------------------------------------------
    // CONFIRMAR TRANSAÇÃO
    // -------------------------------------------------

    if (!$conn->commit()) {
        throw new Exception('Não foi possível confirmar a venda no banco.');
    }

    responder(true, 'Venda realizada com sucesso!', [
        'idVenda' => $idVenda,
        'formaDePagamento' => $formaPagamento,
        'valor' => $valorTotal
    ]);

} catch (Throwable $e) {

    $conn->rollback();

    error_log('Erro ao finalizar venda: ' . $e->getMessage());

    responder(
        false,
        $e->getMessage()
    );

} finally {

    $conn->close();
}
?>
