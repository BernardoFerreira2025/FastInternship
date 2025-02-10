<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 
session_start(); // Iniciar sessão para passar mensagens entre páginas

// Ativar exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST['subject']);
    $message = nl2br(htmlspecialchars($_POST['message']));

    if (!$email) {
        $_SESSION['email_status'] = "error|Erro: E-mail inválido.";
        header("Location: contact.php");
        exit();
    }

    $mail = new PHPMailer(true);
    try {
        // Configuração SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'fastinternship@gmail.com';
        $mail->Password   = 'nrwm gvdd qodk adnx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Configuração do e-mail
        $mail->setFrom($email, $name); 
        $mail->addAddress('fastinternship@gmail.com', 'FastInternship'); 
        $mail->addReplyTo($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Mensagem de Contacto: $subject";
        $mail->Body    = "
            <h2>Nova Mensagem de Contacto</h2>
            <p><strong>Nome:</strong> $name</p>
            <p><strong>E-mail:</strong> $email</p>
            <p><strong>Assunto:</strong> $subject</p>
            <p><strong>Mensagem:</strong><br> $message</p>
        ";

        // Tentativa de envio de e-mail
        if ($mail->send()) {
            $_SESSION['email_status'] = "success|✅ Mensagem enviada com sucesso!";
        } else {
            $_SESSION['email_status'] = "error|❌ Erro ao enviar e-mail.";
        }
    } catch (Exception $e) {
        $_SESSION['email_status'] = "error|❌ Erro ao enviar e-mail.";
    }

    // Redireciona para contact.php após o envio
    header("Location: contact.php");
    exit();
}
?>
