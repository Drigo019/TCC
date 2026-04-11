<?php
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        $nome  = $_POST["nome"];
        $email = $_POST["email"];
        $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); 

        $sql  = "INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $senha);
        $result = mysqli_stmt_execute($stmt);

        if ($result) 
            {
                echo "<script>alert('Cadastro feito com sucesso!!');</script>";
                echo "<script>window.location.href='Tela_Login.html';</script>";
            }
        else 
            {
                echo "<script>alert('Erro ao cadastrar. Tente novamente.');</script>";
            }
    }   