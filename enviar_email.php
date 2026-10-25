
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';

function enviarEmail($destinatario, $assunto, $mensagemHTML)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // Substitua pelo endereço Gmail que enviará os e-mails.
        $mail->Username   = 'leonardoluis1427@gmail.com';

        // Use a senha de aplicativo criada no Google.
        $mail->Password   = 'syjo fzta xbzg hybr';

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($mail->Username, 'Container do Queijo');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $assunto;
        $mail->Body    = $mensagemHTML;
        $mail->AltBody = strip_tags(str_replace(
            ['<br>', '<br/>', '<br />'],
            "\n",
            $mensagemHTML
        ));

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('Erro no envio de e-mail: ' . $mail->ErrorInfo);
        return false;
    }
}
