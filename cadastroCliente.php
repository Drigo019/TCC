<?php
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        $nome  = $_POST["nome"];
        $email = $_POST["email"];
        $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); 
        $cpf = $_POST["cpf"];

        $sql  = "INSERT INTO usuario (nome, email, cpf, senha) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $cpf, $senha);
        $result = mysqli_stmt_execute($stmt);

        $rua  = $_POST["rua"];
        $numero = $_POST["numero"];
        $bairro = $_POST["bairro"]; 

        $sql  = "INSERT INTO endereco_cliente (rua, numero, bairro) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $rua, $numero, $bairro);
        $result = mysqli_stmt_execute($stmt);

        if ($result) 
            {
                echo "<script>alert('Cadastro feito com sucesso!!');</script>";
                echo "<script>window.location.href='TelaLogin.html';</script>";
            }
        else 
            {
                echo "<script>alert('Erro ao cadastrar. Tente novamente.');</script>";
            }
    }   