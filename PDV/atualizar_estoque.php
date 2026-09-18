<?php

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');


/* RECEBER DADOS */

$dados = json_decode(
    file_get_contents("php://input"),
    true
);


$idProduto = intval(
    $dados['idProduto'] ?? 0
);


$quantidade = intval(
    $dados['quantidade'] ?? 0
);


/* VALIDAR */

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


/* ATUALIZAR ESTOQUE */

$sql = "
    UPDATE produtos

    SET estoque = estoque + $quantidade

    WHERE idProduto = $idProduto
";


$resultado = mysqli_query(
    $conn,
    $sql
);


if (!$resultado) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" =>
            "Erro ao atualizar estoque: " .
            mysqli_error($conn)
    ]);

    exit;

}


/* PEGAR NOVO ESTOQUE */

$sqlEstoque = "
    SELECT estoque
    FROM produtos
    WHERE idProduto = $idProduto
";


$resultadoEstoque =
    mysqli_query(
        $conn,
        $sqlEstoque
    );


if (!$resultadoEstoque) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" =>
            "Erro ao consultar estoque."
    ]);

    exit;

}


$produto =
    mysqli_fetch_assoc(
        $resultadoEstoque
    );


/* RETORNAR */

echo json_encode([

    "sucesso" => true,

    "mensagem" =>
        "Estoque atualizado!",

    "estoque" =>
        intval($produto['estoque'])

]);

?>