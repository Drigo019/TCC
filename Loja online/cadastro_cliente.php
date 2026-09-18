<?php

require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome  = $_POST["nome"] ?? "";
    $email = $_POST["email"] ?? "";
    $senha = $_POST["senha"] ?? "";
    $cpf   = $_POST["cpf"] ?? "";

    $senha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, cpf, senha)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        die("Erro ao preparar SQL: " . mysqli_error($conexao));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ssss",
        $nome,
        $email,
        $cpf,
        $senha
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Erro ao cadastrar usuário: " . mysqli_stmt_error($stmt));
    }

    echo "<script>
            alert('Cadastro feito com sucesso!');
            window.location.href = 'inicio.php';
          </script>";
}
?>