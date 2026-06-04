<?php
include 'conexao.php';

$nome = $_POST['nome'];
$preco = $_POST['preco'];
$estoque = $_POST['estoque'];
$codigo = $_POST['codigo_barras'];

$sql = "INSERT INTO produtos
(nome, preco, estoque, codigo_barras)
VALUES
('$nome', '$preco', '$estoque', '$codigo')";

$conn->query($sql);

echo "Produto cadastrado com sucesso!";
?>