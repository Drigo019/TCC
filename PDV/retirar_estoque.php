<?php

include 'conexao.php';

header(
    'Content-Type: application/json; charset=utf-8'
);


/* =========================================
   RECEBE OS DADOS
========================================= */

$dados =
    json_decode(
        file_get_contents("php://input"),
        true
    );


if (!$dados) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Dados inválidos."
    ]);

    exit;
}


/* =========================================
   PEGA OS VALORES
========================================= */

$idProduto =
    intval(
        $dados['idProduto'] ?? 0
    );


$quantidade =
    intval(
        $dados['quantidade'] ?? 0
    );


/* =========================================
   VALIDA
========================================= */

if ($idProduto <= 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Produto inválido."
    ]);

    exit;
}


if ($quantidade <= 0) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Quantidade inválida."
    ]);

    exit;
}


/* =========================================
   BUSCA ESTOQUE ATUAL
========================================= */

$sql =
    "SELECT estoque
     FROM produtos
     WHERE idProduto = $idProduto";


$resultado =
    mysqli_query(
        $conn,
        $sql
    );


if (!$resultado) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" =>
            "Erro ao consultar estoque: " .
            mysqli_error($conn)
    ]);

    exit;
}


$produto =
    mysqli_fetch_assoc(
        $resultado
    );


if (!$produto) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Produto não encontrado."
    ]);

    exit;
}


$estoqueAtual =
    intval(
        $produto['estoque']
    );


/* =========================================
   NÃO DEIXA ESTOQUE FICAR NEGATIVO
========================================= */

if ($quantidade > $estoqueAtual) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" =>
            "Estoque insuficiente. " .
            "Disponível: " .
            $estoqueAtual
    ]);

    exit;
}


/* =========================================
   RETIRA DO ESTOQUE
========================================= */

$sqlUpdate =
    "UPDATE produtos
     SET estoque = estoque - $quantidade
     WHERE idProduto = $idProduto";


$resultadoUpdate =
    mysqli_query(
        $conn,
        $sqlUpdate
    );


if (!$resultadoUpdate) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" =>
            "Erro ao retirar estoque: " .
            mysqli_error($conn)
    ]);

    exit;
}


/* =========================================
   NOVO ESTOQUE
========================================= */

$novoEstoque =
    $estoqueAtual - $quantidade;


/* =========================================
   RESPOSTA
========================================= */

echo json_encode([

    "sucesso" => true,

    "mensagem" =>
        "Estoque retirado com sucesso.",

    "estoque" =>
        $novoEstoque

]);


mysqli_close($conn);

?>