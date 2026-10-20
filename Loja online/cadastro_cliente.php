
<?php

session_start();

require_once __DIR__ . "/conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acesso inválido.");
}

$nome  = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";
$cpf   = trim($_POST["cpf"] ?? "");

// Validar campos obrigatórios
if ($nome === "" || $email === "" || $senha === "" || $cpf === "") {
    echo "<script>
        alert('Preencha todos os campos obrigatórios!');
        history.back();
    </script>";
    exit;
}

// Criptografar a senha
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
    if (mysqli_stmt_errno($stmt) === 1062) {
        $stmt->close();

        echo "<script>
            alert('Este CPF ou e-mail já está cadastrado!');
            history.back();
        </script>";
        exit;
    }

    die("Erro ao cadastrar usuário: " . mysqli_stmt_error($stmt));
}

// Recuperar o ID do usuário recém-cadastrado
$idUsuario = mysqli_insert_id($conexao);

// Criar a sessão de login
session_regenerate_id(true);

$_SESSION['cliente_id'] = $idUsuario;
$_SESSION['nome_usuario'] = $nome;
$_SESSION['email_usuario'] = $email;
$_SESSION['cpf_usuario'] = $cpf;
$_SESSION['logado'] = true;
$_SESSION['acessoPDV'] = false;

$stmt->close();

// Redirecionar para a loja com o cliente já logado
header("Location: inicio.php");
exit;

?>
