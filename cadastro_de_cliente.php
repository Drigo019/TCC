<?php
    require("conexao.php");
    require("Tela_cadastro.html");

    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    if ($_SERVER["REQUEST_METHOD"] === "POST")
        {
            $dados = "INSERT INTO usuario (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

            echo "<script>alert('Cliente cadastrado com sucesso!!');</script>";
        }

    