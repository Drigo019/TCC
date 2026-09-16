<?php

include 'conexao.php';

header('Content-Type: application/json; charset=utf-8');


/* ============================================
   RECEBER CÓDIGO
============================================ */

$codigo = $_GET['codigo'] ?? '';

$codigo = trim($codigo);


/* ============================================
   VERIFICAR CÓDIGO
============================================ */

if ($codigo === '') {

    echo json_encode([
        "erro" => true,
        "mensagem" => "Código não informado"
    ]);

    exit;
}


/* ============================================
   GARANTIR QUE É NUMÉRICO
============================================ */

if (!ctype_digit($codigo)) {

    echo json_encode([
        "erro" => true,
        "mensagem" => "Código inválido"
    ]);

    exit;
}


$codigo = intval($codigo);


/* ============================================
   CONSULTAR BANCO
============================================ */

$sql = "
    SELECT *
    FROM produtos
    WHERE codigoDeBarras = $codigo
";


$resultado = mysqli_query($conn, $sql);


if (!$resultado) {

    echo json_encode([
        "erro" => true,
        "mensagem" =>
            "Erro no banco: " .
            mysqli_error($conn)
    ]);

    exit;
}


/* ============================================
   VERIFICAR RESULTADO
============================================ */

if (mysqli_num_rows($resultado) === 0) {

    echo json_encode([
        "erro" => true,
        "mensagem" => "Produto não encontrado"
    ]);

    exit;
}


/* ============================================
   PEGAR PRODUTO
============================================ */

$produto =
    mysqli_fetch_assoc($resultado);


/* ============================================
   RETORNAR JSON
============================================ */

echo json_encode(
    $produto,
    JSON_UNESCAPED_UNICODE
);


mysqli_free_result($resultado);

mysqli_close($conn);

?>