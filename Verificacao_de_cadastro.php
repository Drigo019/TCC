<?php
    require("conexao.php");

    function clienteExiste(PDO $pdo, string $email): bool 
        {
            $stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            return $stmt->rowCount() > 0;
        }

    if (clienteExiste($pdo, $email)) 
        {
            echo "Cliente encontrado!";
        } 
    else 
        {
            echo "Nenhuma conta com esse e-mail.";
        }