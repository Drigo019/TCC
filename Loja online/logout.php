
<?php

session_start();

// Encerrar a sessão do cliente
unset(
    $_SESSION['cliente_id'],
    $_SESSION['nome_usuario'],
    $_SESSION['email_usuario'],
    $_SESSION['cpf_usuario'],
    $_SESSION['logado']
);

// Regenerar o identificador da sessão
session_regenerate_id(true);

// Voltar para a loja
header("Location: inicio.php");
exit;

?>
