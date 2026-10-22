
<?php

session_start();

require_once __DIR__ . '/Loja Online/conexao.php';

$conn = $conexao;

$token = trim($_GET['token'] ?? '');
$erro = '';
$tokenValido = false;
$tokenHash = '';

// ======================================================
// FUNÇÃO PARA VALIDAR O TOKEN
// ======================================================

function buscarTokenValido($conn, $tokenHash)
{
    $sql = "SELECT id, idUsuario, expira_em, utilizado
            FROM recuperacao_senha
            WHERE token_hash = ?
              AND utilizado = 0
              AND expira_em > NOW()
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Erro ao consultar token: " . $conn->error);
        return null;
    }

    $stmt->bind_param("s", $tokenHash);

    if (!$stmt->execute()) {
        error_log("Erro ao executar consulta do token: " . $stmt->error);
        $stmt->close();
        return null;
    }

    $resultado = $stmt->get_result();
    $token = $resultado->fetch_assoc();

    $stmt->close();

    return $token ?: null;
}

// ======================================================
// VALIDAR TOKEN RECEBIDO PELO LINK
// ======================================================

if (preg_match('/^[a-f0-9]{64}$/', $token)) {
    $tokenHash = hash('sha256', $token);
    $registroToken = buscarTokenValido($conn, $tokenHash);
    $tokenValido = ($registroToken !== false);
}

// ======================================================
// PROCESSAR NOVA SENHA
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = trim($_POST['token'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    $tokenValido = false;
    $registroToken = false;

    if (preg_match('/^[a-f0-9]{64}$/', $token)) {
        $tokenHash = hash('sha256', $token);
        $registroToken = buscarTokenValido($conn, $tokenHash);
        $tokenValido = ($registroToken !== false);
    }

    if (!$tokenValido) {
        $erro = 'Este link é inválido, expirou ou já foi utilizado.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não coincidem.';
    } else {

        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

        if ($hashSenha === false) {
            $erro = 'Não foi possível gerar a nova senha. Tente novamente.';
        } elseif (!$conn->begin_transaction()) {
            $erro = 'Não foi possível iniciar a alteração da senha. Tente novamente.';
        } else {

            try {

                // Bloquear o registro enquanto a senha é alterada.
                $stmt = $conn->prepare(
                    'SELECT idUsuario
                     FROM recuperacao_senha
                     WHERE token_hash = ?
                       AND utilizado = 0
                       AND expira_em > NOW()
                     LIMIT 1
                     FOR UPDATE'
                );

                if (!$stmt) {
                    throw new Exception('Falha ao validar token.');
                }

                $stmt->bind_param('s', $tokenHash);

                if (!$stmt->execute()) {
                    $stmt->close();
                    throw new Exception('Falha ao consultar token.');
                }

                $resultado = $stmt->get_result();
                $registro = $resultado->fetch_assoc();
                $stmt->close();

                if (!$registro) {
                    throw new Exception(
                        'Este link é inválido, expirou ou já foi utilizado.'
                    );
                }

                $idUsuario = (int) $registro['idUsuario'];

                // Atualizar a senha do usuário.
                $stmt = $conn->prepare(
                    'UPDATE usuarios
                     SET senha = ?
                     WHERE idUsuario = ?
                     LIMIT 1'
                );

                if (!$stmt) {
                    throw new Exception('Falha ao preparar atualização.');
                }

                $stmt->bind_param('si', $hashSenha, $idUsuario);

                if (!$stmt->execute()) {
                    $stmt->close();
                    throw new Exception('Falha ao atualizar senha.');
                }

                if ($stmt->affected_rows !== 1) {
                    $stmt->close();
                    throw new Exception('Não foi possível atualizar a senha.');
                }

                $stmt->close();

                // Marcar o token como utilizado.
                $stmt = $conn->prepare(
                    'UPDATE recuperacao_senha
                     SET utilizado = 1
                     WHERE token_hash = ?
                       AND utilizado = 0'
                );

                if (!$stmt) {
                    throw new Exception('Falha ao finalizar recuperação.');
                }

                $stmt->bind_param('s', $tokenHash);

                if (!$stmt->execute() || $stmt->affected_rows !== 1) {
                    $stmt->close();
                    throw new Exception('Falha ao finalizar recuperação.');
                }

                $stmt->close();

                // Excluir outros tokens ainda não utilizados.
                $stmt = $conn->prepare(
                    'DELETE FROM recuperacao_senha
                     WHERE idUsuario = ?
                       AND utilizado = 0'
                );

                if (!$stmt) {
                    throw new Exception('Falha ao invalidar outros tokens.');
                }

                $stmt->bind_param('i', $idUsuario);

                if (!$stmt->execute()) {
                    $stmt->close();
                    throw new Exception('Falha ao invalidar outros tokens.');
                }

                $stmt->close();

                // Confirmar as alterações.
                if (!$conn->commit()) {
                    throw new Exception('Falha ao confirmar alteração.');
                }

                header('Location: /TCC/Loja%20Online/inicio.php?senha_redefinida=1');
                exit;

            } catch (Throwable $e) {

                $conn->rollback();

                error_log(
                    'Erro na redefinição de senha: ' . $e->getMessage()
                );

                if (
                    $e->getMessage() ===
                    'Este link é inválido, expirou ou já foi utilizado.'
                ) {
                    $erro = $e->getMessage();
                } else {
                    $erro = 'Não foi possível alterar a senha. Tente novamente.';
                }
            }
        }
    }
}

// ======================================================
// PREPARAR TOKEN PARA EXIBIÇÃO
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // O token será enviado novamente pelo formulário.
} elseif (!$tokenValido) {
    $token = '';
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Redefinir senha - Container do Queijo</title>

    <style>
        * {
            box-sizing: border-box;
        }

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
        }

        h1 {
            margin-top: 0;
            color: #70451f;
            font-size: 25px;
        }

        p {
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d5c9bb;
            border-radius: 9px;
            font-size: 16px;
        }

        input:focus {
            outline: 2px solid #b78a5b;
            border-color: #70451f;
        }

        button,
        .voltar {
            display: block;
            width: 100%;
            margin-top: 22px;
            padding: 14px;
            border: 0;
            border-radius: 9px;
            background: #70451f;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover,
        .voltar:hover {
            background: #523216;
        }

        .erro {
            padding: 12px;
            border-radius: 8px;
            background: #fff0ef;
            color: #a32620;
        }
    </style>
</head>

<body>

    <main class="cartao">

        <h1>Crie uma nova senha</h1>

        <?php if ($erro !== ''): ?>
            <p class="erro">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($tokenValido): ?>

            <p>Escolha uma nova senha para sua conta.</p>

            <form method="POST" action="redefinir_senha.php">

                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="senha">Nova senha</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <label for="confirmar_senha">Confirme a nova senha</label>

                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    minlength="8"
                    autocomplete="new-password"
                    required
                >

                <button type="submit">Salvar nova senha</button>

            </form>

        <?php elseif ($erro === ''): ?>

            <p>Este link é inválido, expirou ou já foi utilizado.</p>

            <a class="voltar" href="inicio.php">Voltar ao login</a>

        <?php else: ?>

            <a class="voltar" href="inicio.php">Voltar ao login</a>

        <?php endif; ?>

    </main>

</body>
</html>
