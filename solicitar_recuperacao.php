
<?php

session_start();

require_once __DIR__ . '/Loja Online/conexao.php';

$conn = $conexao;

require_once __DIR__ . '/enviar_email.php';

header('Content-Type: text/html; charset=utf-8');

// ======================================================
// EXIBIR RESULTADO
// ======================================================

function finalizar($mensagem)
{
    echo '<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperação de senha - Container do Queijo</title>

        <style>
            * { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                background: #f5f1e8;
                font-family: Arial, sans-serif;
                color: #352719;
            }

            .cartao {
                width: 100%;
                max-width: 430px;
                padding: 32px;
                background: white;
                border-radius: 18px;
                box-shadow: 0 8px 28px #00000014;
                text-align: center;
            }

            h2 { color: #70451f; }

            p { line-height: 1.6; }

            a {
                display: inline-block;
                margin-top: 14px;
                padding: 12px 20px;
                background: #70451f;
                color: white;
                text-decoration: none;
                border-radius: 9px;
            }

            a:hover { background: #523216; }
        </style>
    </head>

    <body>
        <div class="cartao">
            <h2>Recuperação de senha</h2>
            <p>' . htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') . '</p>
            <a href="/TCC/Loja%20Online/inicio.php">Voltar ao login</a>
        </div>
    </body>
    </html>';

    exit;
}

// ======================================================
// VERIFICAR MÉTODO
// ======================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    finalizar('Solicitação inválida. Volte ao login e tente novamente.');
}

// ======================================================
// RECEBER CPF
// ======================================================

$cpfRecebido = trim($_POST['cpf'] ?? '');
$cpf = preg_replace('/\D/', '', $cpfRecebido);

$mensagemPadrao =
    'Se o CPF informado estiver cadastrado, você receberá um e-mail com as instruções de recuperação.';

if (strlen($cpf) !== 11) {
    finalizar($mensagemPadrao);
}

// ======================================================
// BUSCAR USUÁRIO PELO CPF
// Compara o CPF sem pontuação para aceitar os dois formatos.
// ======================================================

$sql = "SELECT idUsuario, email
        FROM usuarios
        WHERE REPLACE(
            REPLACE(
                REPLACE(cpf, '.', ''),
                '-', ''
            ),
            ' ',
            ''
        ) = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log('Recuperação: erro ao preparar consulta: ' . $conn->error);
    finalizar($mensagemPadrao);
}

$stmt->bind_param('s', $cpf);

if (!$stmt->execute()) {
    error_log('Recuperação: erro na consulta: ' . $stmt->error);
    $stmt->close();
    finalizar($mensagemPadrao);
}

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
$stmt->close();

if (!$usuario || empty($usuario['email']) ||
    !filter_var($usuario['email'], FILTER_VALIDATE_EMAIL)) {
    finalizar($mensagemPadrao);
}

$idUsuario = (int) $usuario['idUsuario'];
$emailDestino = $usuario['email'];

// ======================================================
// GERAR TOKEN SEGURO
// ======================================================

try {
    $token = bin2hex(random_bytes(32));
} catch (Throwable $e) {
    error_log('Recuperação: falha ao gerar token.');
    finalizar($mensagemPadrao);
}

$tokenHash = hash('sha256', $token);

// ======================================================
// SUBSTITUIR SOLICITAÇÕES ANTERIORES
// ======================================================

$stmt = $conn->prepare(
    'DELETE FROM recuperacao_senha WHERE idUsuario = ?'
);

if (!$stmt) {
    error_log('Recuperação: erro ao preparar limpeza: ' . $conn->error);
    finalizar($mensagemPadrao);
}

$stmt->bind_param('i', $idUsuario);

if (!$stmt->execute()) {
    error_log('Recuperação: erro ao remover tokens: ' . $stmt->error);
    $stmt->close();
    finalizar($mensagemPadrao);
}

$stmt->close();

// ======================================================
// SALVAR TOKEN COM VALIDADE DE 15 MINUTOS
// ======================================================

$stmt = $conn->prepare(
    'INSERT INTO recuperacao_senha
        (idUsuario, token_hash, expira_em, utilizado)
     VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0)'
);

if (!$stmt) {
    error_log('Recuperação: erro ao preparar token: ' . $conn->error);
    finalizar($mensagemPadrao);
}

$stmt->bind_param('is', $idUsuario, $tokenHash);

if (!$stmt->execute()) {
    error_log('Recuperação: erro ao salvar token: ' . $stmt->error);
    $stmt->close();
    finalizar($mensagemPadrao);
}

$stmt->close();

// ======================================================
// MONTAR LINK
// redefinir_senha.php está na raiz de TCC.
// ======================================================

$protocolo = (
    !empty($_SERVER['HTTPS']) &&
    $_SERVER['HTTPS'] !== 'off'
) ? 'https://' : 'http://';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

$link = $protocolo . $host
      . '/TCC/redefinir_senha.php?token='
      . urlencode($token);

// ======================================================
// PREPARAR E-MAIL
// ======================================================

$assunto = 'Recuperação de senha - Container do Queijo';

$mensagemHTML = '
<div style="font-family:Arial,sans-serif;max-width:520px;margin:auto;color:#352719">
    <h2 style="color:#70451f">Redefina sua senha</h2>

    <p>Recebemos uma solicitação para redefinir a senha da sua conta no Container do Queijo.</p>

    <p>Para escolher uma nova senha, clique no botão abaixo:</p>

    <p style="text-align:center;margin:28px 0">
        <a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '"
           style="background:#70451f;color:white;padding:14px 22px;
           text-decoration:none;border-radius:8px;display:inline-block">
           Criar nova senha
        </a>
    </p>

    <p>Este link expira em 15 minutos e só pode ser usado uma vez.</p>

    <p>Se você não solicitou a recuperação, ignore este e-mail.</p>
</div>';

// ======================================================
// ENVIAR E-MAIL
// ======================================================

try {
    $enviado = enviarEmail($emailDestino, $assunto, $mensagemHTML);
} catch (Throwable $e) {
    error_log('Recuperação: exceção no envio de e-mail: ' . $e->getMessage());
    $enviado = false;
}

if (!$enviado) {
    error_log(
        'Recuperação: falha ao enviar e-mail para o usuário ID ' . $idUsuario
    );

    // Invalidar o token, pois o link não foi enviado.
    $stmt = $conn->prepare(
        'DELETE FROM recuperacao_senha WHERE idUsuario = ?'
    );

    if ($stmt) {
        $stmt->bind_param('i', $idUsuario);
        $stmt->execute();
        $stmt->close();
    }
}

// Mantém uma resposta genérica para não revelar se o CPF está cadastrado.
finalizar($mensagemPadrao);

?>
