<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');


/* =====================================================
   RECEBER DADOS
===================================================== */

$dados = json_decode(
    file_get_contents("php://input"),
    true
);


if (!$dados) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível receber os dados da venda."
    ]);

    exit;
}


$produtos = $dados['produtos'] ?? [];
$valor = floatval($dados['valor'] ?? 0);
$formaPagamento = $dados['formaPagamento'] ?? '';


if (empty($produtos)) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Nenhum produto foi informado."
    ]);

    exit;
}


if ($valor <= 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Valor da venda inválido."
    ]);

    exit;
}



/* =====================================================
   INICIAR TRANSAÇÃO
===================================================== */

mysqli_begin_transaction($conn);


try {


    /* =================================================
       VERIFICAR ESTOQUE
    ================================================= */

    foreach ($produtos as $produto) {

        $idProduto =
            intval($produto['id']);

        $quantidade =
            intval($produto['quantidade']);


        if ($idProduto <= 0) {

            throw new Exception(
                "ID de produto inválido."
            );

        }


        if ($quantidade <= 0) {

            throw new Exception(
                "Quantidade inválida."
            );

        }


        /*
            Busca o estoque atual.
        */

        $sqlEstoque = "
            SELECT estoque
            FROM produtos
            WHERE idProduto = $idProduto
            FOR UPDATE
        ";


        $resultadoEstoque =
            mysqli_query(
                $conn,
                $sqlEstoque
            );


        if (!$resultadoEstoque) {

            throw new Exception(
                "Erro ao consultar estoque: " .
                mysqli_error($conn)
            );

        }


        $produtoBanco =
            mysqli_fetch_assoc(
                $resultadoEstoque
            );


        if (!$produtoBanco) {

            throw new Exception(
                "Produto ID " .
                $idProduto .
                " não encontrado."
            );

        }


        $estoqueAtual =
            intval(
                $produtoBanco['estoque']
            );


        /*
            Verifica se existe estoque suficiente.
        */

        if ($quantidade > $estoqueAtual) {

            throw new Exception(

                "Estoque insuficiente para o produto ID " .
                $idProduto .
                ".\n\n" .

                "Estoque disponível: " .
                $estoqueAtual .
                "\n" .

                "Quantidade solicitada: " .
                $quantidade

            );

        }

    }



    /* =================================================
       REGISTRAR VENDA
    ================================================= */

    $valorBanco =
        number_format(
            $valor,
            2,
            '.',
            ''
        );


        $sqlVenda = "
        INSERT INTO vendas
        (valor, data, formaPagamento)
        VALUES
        ($valorBanco, NOW(), '$formaPagamento')
    ";


    $resultadoVenda =
        mysqli_query(
            $conn,
            $sqlVenda
        );


    if (!$resultadoVenda) {

        throw new Exception(
            "Erro ao registrar venda no banco:\n\n" .
            mysqli_error($conn)
        );

    }


    /*
        Pega o ID da venda recém-criada.
    */

    $idVenda =
        mysqli_insert_id($conn);



    /* =================================================
       BAIXAR ESTOQUE
    ================================================= */

    foreach ($produtos as $produto) {

        $idProduto =
            intval($produto['id']);

        $quantidade =
            intval($produto['quantidade']);


        $sqlBaixa = "
            UPDATE produtos
            SET estoque = estoque - $quantidade
            WHERE idProduto = $idProduto
        ";


        $resultadoBaixa =
            mysqli_query(
                $conn,
                $sqlBaixa
            );


        if (!$resultadoBaixa) {

            throw new Exception(
                "Erro ao baixar estoque do produto ID " .
                $idProduto .
                ":\n\n" .
                mysqli_error($conn)
            );

        }

    }



    /* =================================================
       CONFIRMAR TRANSAÇÃO
    ================================================= */

    mysqli_commit($conn);


    /* =================================================
       RETORNAR SUCESSO
    ================================================= */

    echo json_encode([

        "sucesso" => true,

        "mensagem" =>
            "Venda realizada com sucesso!",

        "idVenda" =>
            $idVenda,

        "formaPagamento" =>
            $formaPagamento

    ]);


} catch (Exception $e) {


    /* =================================================
       DESFAZER TUDO
    ================================================= */

    mysqli_rollback($conn);


    echo json_encode([

        "sucesso" => false,

        "mensagem" =>
            $e->getMessage()

    ]);

}

?>