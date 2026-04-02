<?php
    $servidor = '127.0.0.1';
    $usuario = 'root';
    $senha = "";
    $banco = 'clientes';
    $porta = '3306';
    $conexao = mysqli_connect($servidor, $usuario, $senha, $banco, $porta);

    global $pdo;

    try 
        {
            $pdo = new PDO($servidor, $usuario, $senha);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    catch (PDOException $erro)
        {
            return ''. $erro->getMessage() .'';
            exit;
        } 
