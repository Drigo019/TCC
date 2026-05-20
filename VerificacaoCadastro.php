<?php
// Inclui o arquivo de conexão com o banco de dados
include("conexao.php");

// Captura o CPF do formulário e já valida se é um CPF válido
// Retorna false se inválido, null se o campo não existir
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_NUMBER_INT);

// Captura a senha do formulário e remove caracteres especiais perigosos
$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

    // Verifica se o CPF é inválido ou não foi enviado
    if (empty($cpf)) 
        {
            echo "<script>alert('CPF inválido!');</script>";
            exit; // Para a execução do código aqui
        }

    // Verifica se a senha está vazia
    if (empty($senha)) 
        {
            echo "<script>alert('Senha inválida!');</script>";
            exit; // Para a execução do código aqui
        }

// Prepara a consulta SQL para buscar a senha do usuário pelo CPF
// O "?" é um placeholder que será substituído pelo CPF com segurança (evita SQL Injection)
$stmt = $conexao->prepare("SELECT senha FROM usuario WHERE cpf = ?");

$cpf = str_replace(['.', '-'], '', $cpf);
$cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

// Substitui o "?" pelo valor de $cpf
// "s" significa que o valor é do tipo string
$stmt->bind_param("s", $cpf);

// Executa a consulta no banco de dados
$stmt->execute();

// Armazena o resultado da consulta na memória para poder usar num_rows
$stmt->store_result();

    // Verifica se nenhum usuário foi encontrado com esse CPF
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
            echo "<script>window.location.href='TelaPrincipal.html';</script>";
        } 
    else 
        {
            // Senha incorreta
            echo "<script>alert('Senha incorreta!');</script>";
        }

// Fecha a consulta e libera os recursos
$stmt->close();
