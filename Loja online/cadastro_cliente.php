
<?php

session_start();

require_once __DIR__ . "/conexao.php";

mysqli_set_charset($conexao, "utf8mb4");

// ======================================================
// FUNÇÃO PARA RETORNAR AO CADASTRO COM UMA MENSAGEM
// ======================================================

function erroCadastro($mensagem)
{
    $_SESSION['dados_cadastro'] = [
        'nome'   => trim($_POST['nome'] ?? ''),
        'email'  => trim($_POST['email'] ?? ''),
        'cpf'    => trim($_POST['cpf'] ?? ''),
        'cep'    => trim($_POST['cep'] ?? ''),
        'rua'    => trim($_POST['rua'] ?? ''),
        'numero' => trim($_POST['numero'] ?? ''),
        'bairro' => trim($_POST['bairro'] ?? '')
    ];

    $_SESSION['mensagem_cadastro'] = $mensagem;
    $_SESSION['abrir_cadastro'] = true;

    header('Location: inicio.php');
    exit;
}

$cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');

if (!preg_match('/^\d{8}$/', $cep)) {
    erroCadastro('Informe um CEP válido com 8 números.');
}

$url = "https://viacep.com.br/ws/" . $cep . "/json/";

$contexto = stream_context_create([
    'http' => [
        'timeout' => 5
    ]
]);

$resposta = @file_get_contents($url, false, $contexto);

if ($resposta === false) {
    erroCadastro(
        'Não foi possível verificar o CEP agora. Tente novamente.'
    );
}

$dadosCEP = json_decode($resposta, true);

if (!is_array($dadosCEP) || !empty($dadosCEP['erro'])) {
    erroCadastro('O CEP informado não foi encontrado.');
}

$dadosCadastro = $_SESSION["dados_cadastro"] ?? [];

unset($_SESSION["dados_cadastro"]);

// ======================================================
// VERIFICAR MÉTODO
// ======================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: inicio.php");
    exit;
}

// ======================================================
// RECEBER DADOS
// ======================================================

$nome = trim($_POST["nome"] ?? "");
$email = trim($_POST["email"] ?? "");
$senha = $_POST["senha"] ?? "";
$confirmarSenha = $_POST["confirmar_senha"] ?? "";
$cpfRecebido = $_POST["cpf"] ?? "";

// Remover pontos, hífen e outros caracteres do CPF.
$cpf = preg_replace('/\D/', '', $cpfRecebido);

// ======================================================
// VALIDAR CAMPOS
// ======================================================

if (
    $nome === "" ||
    $email === "" ||
    $senha === "" ||
    $confirmarSenha === "" ||
    $cpf === ""
) {
    erroCadastro("Preencha todos os campos obrigatórios.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    erroCadastro("Digite um endereço de e-mail válido.");
}

if (strlen($senha) < 8) {
    erroCadastro("A senha deve possuir pelo menos 8 caracteres.");
}

if ($senha !== $confirmarSenha) {
    erroCadastro("As senhas digitadas são diferentes.");
}

if (strlen($nome) > 150) {
    erroCadastro("O nome informado é muito longo.");
}

// ======================================================
// VALIDAR OS DÍGITOS DO CPF
// ======================================================

function cpfValido($cpf)
{
    if (strlen($cpf) !== 11) {
        return false;
    }

    // Rejeitar CPFs com todos os dígitos iguais.
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    // Calcular o primeiro dígito verificador.
    $soma = 0;

    for ($i = 0; $i < 9; $i++) {
        $soma += (int)$cpf[$i] * (10 - $i);
    }

    $resto = ($soma * 10) % 11;
    $digito1 = ($resto === 10) ? 0 : $resto;

    if ($digito1 !== (int)$cpf[9]) {
        return false;
    }

    // Calcular o segundo dígito verificador.
    $soma = 0;

    for ($i = 0; $i < 10; $i++) {
        $soma += (int)$cpf[$i] * (11 - $i);
    }

    $resto = ($soma * 10) % 11;
    $digito2 = ($resto === 10) ? 0 : $resto;

    return $digito2 === (int)$cpf[10];
}

if (!cpfValido($cpf)) {
    erroCadastro(
        "O CPF informado é inválido. Confira os números digitados."
    );
}

// ======================================================
// FORMATAR CPF COMO O LOGIN ESPERA
// ======================================================

$cpfFormatado =
    substr($cpf, 0, 3) . "." .
    substr($cpf, 3, 3) . "." .
    substr($cpf, 6, 3) . "-" .
    substr($cpf, 9, 2);

// ======================================================
// VERIFICAR SE O CPF JÁ ESTÁ CADASTRADO
// Aceita CPF salvo com ou sem pontuação.
// ======================================================

$sql = "
    SELECT idUsuario
    FROM usuarios
    WHERE REPLACE(
        REPLACE(
            REPLACE(cpf, '.', ''),
            '-', ''
        ),
        ' ',
        ''
    ) = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    erroCadastro("Não foi possível validar o CPF no momento.");
}

$stmt->bind_param("s", $cpf);
$stmt->execute();

$resultado = $stmt->get_result();
$cpfExiste = $resultado->num_rows > 0;

$stmt->close();

if ($cpfExiste) {
    erroCadastro(
        "Este CPF já está cadastrado. Entre na sua conta ou utilize outro CPF."
    );
}

// ======================================================
// VERIFICAR SE O E-MAIL JÁ ESTÁ CADASTRADO
// ======================================================

$sql = "
    SELECT idUsuario
    FROM usuarios
    WHERE email = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    erroCadastro("Não foi possível validar o e-mail no momento.");
}

$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();
$emailExiste = $resultado->num_rows > 0;

$stmt->close();

if ($emailExiste) {
    erroCadastro(
        "Este e-mail já está cadastrado. Utilize outro endereço de e-mail."
    );
}

// ======================================================
// CRIPTOGRAFAR A SENHA
// ======================================================

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

if ($senhaHash === false) {
    erroCadastro("Não foi possível proteger a senha. Tente novamente.");
}

// ======================================================
// CADASTRAR USUÁRIO
// ======================================================

$sql = "
    INSERT INTO usuarios (nome, email, cpf, senha)
    VALUES (?, ?, ?, ?)
";

$stmt = $conexao->prepare($sql);

if (!$stmt) {
    erroCadastro("Não foi possível preparar o cadastro.");
}

$stmt->bind_param(
    "ssss",
    $nome,
    $email,
    $cpfFormatado,
    $senhaHash
);

if (!$stmt->execute()) {

    // Tratar duplicidade mesmo se dois cadastros
    // forem enviados praticamente ao mesmo tempo.
    if ($stmt->errno === 1062) {
        $stmt->close();

        erroCadastro(
            "Este CPF ou e-mail já está cadastrado."
        );
    }

    error_log("Erro ao cadastrar usuário: " . $stmt->error);

    $stmt->close();

    erroCadastro(
        "Não foi possível concluir o cadastro. Tente novamente."
    );
}

// ======================================================
// CRIAR SESSÃO DO CLIENTE
// ======================================================

$idUsuario = $conexao->insert_id;

$stmt->close();

session_regenerate_id(true);

$_SESSION["cliente_id"] = $idUsuario;
$_SESSION["nome_usuario"] = $nome;
$_SESSION["email_usuario"] = $email;
$_SESSION["cpf_usuario"] = $cpfFormatado;
$_SESSION["logado"] = true;
$_SESSION["acessoPDV"] = false;

// ======================================================
// REDIRECIONAR COM LOGIN AUTOMÁTICO
// ======================================================

header("Location: inicio.php");
exit;

?>
