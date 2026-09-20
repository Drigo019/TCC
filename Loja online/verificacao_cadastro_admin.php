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
// PEGAR DADOS DO FORMULÁRIO
// ======================================================

$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

// Remover máscara do CPF
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
// FORMATAR CPF
// ======================================================

$cpfBanco = $cpf;

if (strlen($cpf) === 11) {
    $cpfBanco = substr($cpf, 0, 3) . "." .
                substr($cpf, 3, 3) . "." .
                substr($cpf, 6, 3) . "-" .
                substr($cpf, 9, 2);
}

// ======================================================
// BUSCAR CLIENTE
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

$stmt->bind_param("s", $cpfBanco);

if (!$stmt->execute()) {
    die("Erro ao executar consulta: " . $stmt->error);
}

$resultado = $stmt->get_result();

// ======================================================
// VERIFICAR USUÁRIO
// ======================================================

if ($resultado->num_rows === 0) {
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

    echo "<script>
        alert('Senha incorreta!');
        history.back();
    </script>";

    exit;
}

// ======================================================
// LOGIN REALIZADO
// ======================================================

// Salvar informações do cliente na sessão
$_SESSION['idCliente'] = (int) $usuario['idUsuario'];
$_SESSION['nomeCliente'] = $usuario['nome'];
$_SESSION['cpfCliente'] = $usuario['cpf'];

// ======================================================
// FECHAR CONSULTA
// ======================================================

$stmt->close();

// ======================================================
// REDIRECIONAR
// ======================================================

echo "<script>
    window.location.href = 'inicio.php';
</script>";

exit;
?>