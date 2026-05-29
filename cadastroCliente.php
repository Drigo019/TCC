<?php
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        $nome  = $_POST["nome"];
        $email = $_POST["email"];
        $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); 
        $cpf = $_POST["cpf"];

        $sql  = "INSERT INTO usuarios (nome, email, cpf, senha) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $nome, $email, $cpf, $senha);
        $result = mysqli_stmt_execute($stmt);

        $rua  = $_POST["rua"];
        $numero = $_POST["numero"];
        $bairro = $_POST["bairro"]; 
        $cep = $_POST["cep"]; 

        $sql  = "INSERT INTO enderecosclientes (rua, numero, bairro, cep) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $rua, $numero, $bairro, $cep);
        $result = mysqli_stmt_execute($stmt);

        if ($result) 
            {
                echo "<script>alert('Cadastro feito com sucesso!!');</script>";
                echo "<script>window.location.href='TelaInicial.html';</script>";
            }
        else 
            {
                echo "<script>alert('Erro ao cadastrar. Tente novamente.');</script>";
            }
    }   