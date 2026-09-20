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

// Remover pontos, traços e qualquer outro caractere
// que não seja número
$cpf = preg_replace('/\D/', '', $cpf);

// ======================================================
// VALIDAR CPF
// ======================================================

if (empty($cpf)) {

    echo "<script>
        alert('CPF inválido!');
        history.back();
    </script>";

    exit;
}

// ======================================================
// VALIDAR SENHA
// ======================================================

if ($senha === '') {

    echo "<script>
        alert('Senha inválida!');
        history.back();
    </script>";

    exit;
}

// ======================================================
// FORMATAR CPF PARA O BANCO
// ======================================================

if (strlen($cpf) === 11) {

    $cpf = substr($cpf, 0, 3) . "." .
           substr($cpf, 3, 3) . "." .
           substr($cpf, 6, 3) . "-" .
           substr($cpf, 9, 2);
}

// ======================================================
// BUSCAR USUÁRIO
// ======================================================

$sql = "
    SELECT idUsuario, nome, cpf, senha
    FROM usuarios
    WHERE cpf = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar consulta: " . $conexao->error);
}

$stmt->bind_param("s", $cpf);

if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}

$resultado = $stmt->get_result();

// ======================================================
// VERIFICAR SE USUÁRIO EXISTE
// ======================================================

if ($resultado->num_rows === 0) {

    echo "<script>
        alert('Usuário não encontrado!');
        history.back();
    </script>";

    $stmt->close();
    exit;
}

// ======================================================
// PEGAR DADOS DO USUÁRIO
// ======================================================

$usuario = $resultado->fetch_assoc();

// ======================================================
// VERIFICAR SENHA
// ======================================================

if (!password_verify($senha, $usuario['senha'])) {

    echo "<script>
        alert('Senha incorreta!');
        history.back();
    </script>";

    $stmt->close();
    exit;
}

// ======================================================
// LOGIN REALIZADO
// ======================================================

// ID do cliente
$_SESSION['idCliente'] = (int) $usuario['idUsuario'];

// Nome do cliente
$_SESSION['nomeCliente'] = $usuario['nome'];

// CPF do cliente
$_SESSION['cpfCliente'] = $usuario['cpf'];

// Indicar que está logado
$_SESSION['logado'] = true;

// ======================================================
// FECHAR CONSULTA
// ======================================================

$stmt->close();

// ======================================================
// IR PARA A LOJA
// ======================================================

header("Location: inicio.php");
exit;

?>