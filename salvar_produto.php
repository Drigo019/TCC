<?php
include 'conexaoPDV.php';

$nome = $_POST['nome'];
$preco = $_POST['preco'];
$estoque = $_POST['estoque'];

$sql = "INSERT INTO produtos (nome, preco, estoque) VALUES ('$nome', '$preco', '$estoque')";

$conn->query($sql);

echo "Produto cadastrado com sucesso!";
?>  