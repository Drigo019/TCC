<?php
    require("conexao.php");

    $id_usuario = $_POST["id_usuario"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $query = "INSERT INTO usuario(id_usuario, email, senha) VALUE('id_usuario','$email', '$senha')";  
    
    header("Location: Tela_Login.html?criado=sucesso");