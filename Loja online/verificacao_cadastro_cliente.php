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
// Deixar somente os números do CPF
    $cpf = preg_replace('/\D/', '', $cpf);
// ======================================================
// DADOS DE ACESSO AO PDV
// ======================================================
// COLOQUE AQUI O CPF DO PDV
    $cpfPDV = '54068951845';
// COLOQUE AQUI A SENHA DO PDV
    $senhaPDV = '0811';
// ======================================================
// VERIFICAR SE É ACESSO AO PDV
// ======================================================
    if ($cpf === $cpfPDV && $senha === $senhaPDV) {
    // Criar sessão do PDV
        $_SESSION['acessoPDV'] = true;
        $_SESSION['logado'] = true;
        $_SESSION['cpfPDV'] = $cpf;
    // Ir para o PDV
        header("Location: ../PDV/");
        exit;
    }
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
// FORMATAR CPF PARA CONSULTAR NO BANCO
// ======================================================
    if (strlen($cpf) === 11) {
        $cpfBanco = substr($cpf, 0, 3) . "." .
                    substr($cpf, 3, 3) . "." .
                    substr($cpf, 6, 3) . "-" .
                    substr($cpf, 9, 2);
    } else {
        $cpfBanco = $cpf;
    }
// ======================================================
// BUSCAR USUÁRIO NA TABELA USUARIOS
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
// LOGIN NORMAL DA LOJA
// ======================================================
    $_SESSION['idCliente'] = (int) $usuario['idUsuario'];
    $_SESSION['nomeCliente'] = $usuario['nome'];
    $_SESSION['cpfCliente'] = $usuario['cpf'];
    $_SESSION['logado'] = true;
// Garantir que não é acesso ao PDV
    $_SESSION['acessoPDV'] = false;
// ======================================================
// FECHAR CONSULTA
// ======================================================
    $stmt->close();
// ======================================================
// IR PARA A LOJA ONLINE
// ======================================================
    header("Location: inicio.php");
    exit;
?>
