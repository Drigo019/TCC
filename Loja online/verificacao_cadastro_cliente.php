
<?php

session_start();

require_once __DIR__ . "/conexao.php";

// ======================================================
// VERIFICAR MÉTODO
// ======================================================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acesso inválido.");
}

// ======================================================
// RECEBER DADOS
// ======================================================
$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

$cpf = preg_replace('/\D/', '', $cpf);

// ======================================================
// DADOS DE ACESSO AO PDV
// ======================================================
$cpfPDV = '54068951845';
$senhaPDV = '0811';

// ======================================================
// VERIFICAR SE É ACESSO AO PDV
// ======================================================
if ($cpf === $cpfPDV && $senha === $senhaPDV) {

    session_regenerate_id(true);

    $_SESSION['acessoPDV'] = true;
    $_SESSION['logado'] = true;
    $_SESSION['cpfPDV'] = $cpf;

    header("Location: ../PDV/");
    exit;
}

// ======================================================
// VALIDAR CPF E SENHA
// ======================================================
if (strlen($cpf) !== 11) {
    echo "<script>
        alert('CPF inválido!');
        history.back();
    </script>";
    exit;
}

if ($senha === '') {
    echo "<script>
        alert('Senha inválida!');
        history.back();
    </script>";
    exit;
}

// ======================================================
// FORMATAR CPF PARA CONSULTA
// ======================================================
$cpfBanco = substr($cpf, 0, 3) . "." .
            substr($cpf, 3, 3) . "." .
            substr($cpf, 6, 3) . "-" .
            substr($cpf, 9, 2);

// ======================================================
// BUSCAR USUÁRIO
// ======================================================
$sql = "
    SELECT idUsuario, nome, cpf, senha, email
    FROM usuarios
    WHERE cpf = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conexao->error);
}

$stmt->bind_param("s", $cpfBanco);

if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}

$resultado = $stmt->get_result();

// ======================================================
// VERIFICAR SE USUÁRIO EXISTE
// ======================================================
if ($resultado->num_rows === 0) {
    $stmt->close();

    echo "<script>
        alert('Usuário não encontrado!');
        history.back();
    </script>";
    exit;
}

$usuario = $resultado->fetch_assoc();

// ======================================================
// VERIFICAR SENHA
// ======================================================
if (!password_verify($senha, $usuario['senha'])) {
    $stmt->close();

    echo "<script>
        alert('Senha incorreta!');
        history.back();
    </script>";
    exit;
}

// ======================================================
// LOGIN NORMAL DA LOJA ONLINE
// ======================================================
session_regenerate_id(true);

$_SESSION['cliente_id'] = $usuario['idUsuario'];
$_SESSION['nome_usuario'] = $usuario['nome'];
$_SESSION['email_usuario'] = $usuario['email'];
$_SESSION['cpf_usuario'] = $usuario['cpf'];

$_SESSION['logado'] = true;
$_SESSION['acessoPDV'] = false;

// ======================================================
// FINALIZAR
// ======================================================
$stmt->close();

header("Location: inicio.php");
exit;

?>
