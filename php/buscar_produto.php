<?php

include 'conexao.php';

$codigo = $_GET['codigo'];

$sql = "SELECT * FROM produtos
        WHERE codigo_barras = '$codigo'";

$resultado = $conn->query($sql);

if($resultado->num_rows > 0){

    $produto = $resultado->fetch_assoc();

    echo json_encode([
        "nome" => $produto['nome'],
        "preco" => $produto['preco']
    ]);

}else{

    echo json_encode([
        "erro" => true
    ]);

}
?>