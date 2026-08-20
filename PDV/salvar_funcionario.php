<?php
include 'conexao.php';

$nome = $_POST['nome'];
$cargo = $_POST['cargo'];

$stmt = $conn->prepare(
    "INSERT INTO funcionarios (nome, cargo) VALUES (?, ?)"
);

$stmt->bind_param("ss", $nome, $cargo);

$stmt->execute();

echo "Funcionário cadastrado com sucesso!";
?>
