<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/enviar_email.php';

$destinatario = 'Leonardoluis1427@gmail.com';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();

    // Ativa o diagnóstico do SMTP
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function ($mensagem, $nivel) {
        echo htmlspecialchars($mensagem) . "<br>\n";
    };

    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;

    // Use os mesmos dados configurados em enviar_email.php
    $mail->Username = 'Leonardoluis1427@gmail.com';
    $mail->Password = 'syjo fzta xbzg hybr';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom($mail->Username, 'Container do Queijo');
    $mail->addAddress($destinatario);

    $mail->isHTML(true);
    $mail->Subject = 'Teste de e-mail - Container do Queijo';
    $mail->Body = '<h2>Funcionou!</h2><p>Teste de envio de e-mail.</p>';
    $mail->AltBody = 'Teste de envio de e-mail.';

    $mail->send();

    echo '<p><strong>E-mail enviado com sucesso!</strong></p>';

} catch (\Throwable $e) {
    echo '<p><strong>Falha no envio:</strong> ';
    echo htmlspecialchars($e->getMessage());
    echo '</p>';
}


