<?php
// Inclui o arquivo de conexão com o banco de dados
include("conexao.php");

// Captura o e-mail do formulário e já valida se é um e-mail válido
// Retorna false se inválido, null se o campo não existir
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

// Captura a senha do formulário e remove caracteres especiais perigosos
$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

    // Verifica se o e-mail é inválido ou não foi enviado
    if ($email === false || $email === null) 
        {
            echo "<script>alert('E-mail inválido!');</script>";
            exit; // Para a execução do código aqui
        }

    // Verifica se a senha está vazia
    if (empty($senha)) 
        {
            echo "<script>alert('Senha inválida!');</script>";
            exit; // Para a execução do código aqui
        }

// Prepara a consulta SQL para buscar a senha do usuário pelo e-mail
// O "?" é um placeholder que será substituído pelo e-mail com segurança (evita SQL Injection)
$stmt = $conexao->prepare("SELECT senha FROM usuario WHERE email = ?");

// Substitui o "?" pelo valor de $email
// "s" significa que o valor é do tipo string
$stmt->bind_param("s", $email);

// Executa a consulta no banco de dados
$stmt->execute();

// Armazena o resultado da consulta na memória para poder usar num_rows
$stmt->store_result();

    // Verifica se nenhum usuário foi encontrado com esse e-mail
    if ($stmt->num_rows === 0) 
        {
            echo "<script>alert('Usuário não encontrado!');</script>";
            exit; // Para a execução do código aqui
        }

// Associa a coluna "senha" do resultado à variável $senhaHash
$stmt->bind_result($senhaHash);

// Busca a linha do resultado e carrega na variável $senhaHash
$stmt->fetch();

// Compara a senha digitada com o hash salvo no banco
// password_verify criptografa a senha digitada e compara com o hash
    if (password_verify($senha, $senhaHash)) 
        {
            // Senha correta - redireciona para a tela principal
            echo "<script>window.location.href='Tela_Principal.html';</script>";
        } 
    else 
        {
            // Senha incorreta
            echo "<script>alert('Senha incorreta!');</script>";
        }

// Fecha a consulta e libera os recursos
$stmt->close();
