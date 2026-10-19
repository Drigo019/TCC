
<?php

session_start();

require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome  = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $cpf   = trim($_POST["cpf"] ?? "");

    // Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

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
        $senhaHash
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Erro ao cadastrar usuário: " . mysqli_stmt_error($stmt));
    }

    session_regenerate_id(true);

$_SESSION['cliente_id'] = mysqli_insert_id($conexao);
$_SESSION['nome_usuario'] = $nome;
$_SESSION['email_usuario'] = $email;
$_SESSION['cpf_usuario'] = $cpf;
$_SESSION['logado'] = true;
$_SESSION['acessoPDV'] = false;

    // Recupera o ID do usuário que acabou de ser cadastrado
    $idUsuario = mysqli_insert_id($conexao);

    // Cria a sessão de login
    session_regenerate_id(true);

    $_SESSION["cliente_id"] = $idUsuario;
    $_SESSION["nome_usuario"] = $nome;
    $_SESSION["email_usuario"] = $email;
    $_SESSION["cpf_usuario"] = $cpf;

    mysqli_stmt_close($stmt);

    // Redireciona o cliente já autenticado
    header("Location: inicio.php");
    exit;
}
?>
