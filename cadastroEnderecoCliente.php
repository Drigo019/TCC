<?php
include("conexao.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") 
    {
        $rua  = $_POST["rua"];
        $numero = $_POST["numero"];
        $bairro = $_POST["bairro"]; 

        $sql  = "INSERT INTO endereco (rua, numero, bairro) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $rua, $numero, $bairro, $id_usuario);
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