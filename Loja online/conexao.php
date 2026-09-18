<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "containerdoqueijo";

$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro na conexão com o banco: " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");

?>