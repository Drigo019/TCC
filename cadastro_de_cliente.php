<?php
    require("conexao.php");
    require("Tela_cadastro.html");

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $sql = "insert into usuario(nome, email, senha) values('$nome', '$email', '$senha')";
    $result = mysqli_query($conexao, $sql);
