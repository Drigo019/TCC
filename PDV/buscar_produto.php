<?php

include 'conexao.php';

$codigo = $_GET['codigo'];

$sql = "SELECT * FROM produtos
        WHERE codigoDeBarras = '$codigo'
        LIMIT 1";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {

    $produto = $resultado->fetch_assoc();

    echo json_encode($produto);

} else {

    echo json_encode([
        "erro" => "Produto não encontrado"
    ]);

}

?>