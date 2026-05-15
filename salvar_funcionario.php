<?php
include 'conexao.php';

$nome = $_POST['nome'];
$cargo = $_POST['cargo'];

$sql = "INSERT INTO funcionarios (nome, cargo) VALUES ('$nome', '$cargo')";

$conn->query($sql);

echo "Funcionário cadastrado com sucesso!";
?>

