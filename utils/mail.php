<?php

namespace utils;

require_once __DIR__ . "/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/PHPMailer/src/SMTP.php";
require_once __DIR__ . "/constants.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    public function send($email, $subject, $message): bool
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Constants::MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Port = Constants::MAIL_PORT;
        $mail->Username = Constants::MAIL_USERNAME;
        $mail->Password = Constants::MAIL_PASSWORD;
        $mail->SMTPSecure = Constants::MAIL_ENCRYPTION;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(Constants::MAIL_FROM_ADDRESS, Constants::MAIL_FROM_NAME);
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail->send();
    }

    public function sendForgot($email, $subject, $data = []): bool
    {
        $template = file_get_contents(__DIR__ . "/templates/forgot.html");

        $template = $this->loadTemplate($template, $data);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Constants::MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Port = Constants::MAIL_PORT;
        $mail->Username = Constants::MAIL_USERNAME;
        $mail->Password = Constants::MAIL_PASSWORD;
        $mail->SMTPSecure = Constants::MAIL_ENCRYPTION;
        $mail->setFrom(Constants::MAIL_FROM_ADDRESS, Constants::MAIL_FROM_NAME);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $template;

        return $mail->send();
    }

    public function sendConfirmation($email, $subject, $data = []): bool
    {
        $template = file_get_contents(__DIR__ . "/templates/confirmation.html");

        $template = $this->loadTemplate($template, $data, 'api/auth/verify');

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Constants::MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Port = Constants::MAIL_PORT;
        $mail->Username = Constants::MAIL_USERNAME;
        $mail->Password = Constants::MAIL_PASSWORD;
        $mail->SMTPSecure = Constants::MAIL_ENCRYPTION;
        $mail->setFrom(Constants::MAIL_FROM_ADDRESS, Constants::MAIL_FROM_NAME);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $template;

        return $mail->send();
    }

    public function sendNotification($email, $subject, $data = []): bool
    {
        $template = file_get_contents(__DIR__ . "/templates/notification.html");

        $template = $this->loadTemplate($template, $data);

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Constants::MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Port = Constants::MAIL_PORT;
        $mail->Username = Constants::MAIL_USERNAME;
        $mail->Password = Constants::MAIL_PASSWORD;
        $mail->SMTPSecure = Constants::MAIL_ENCRYPTION;
        $mail->setFrom(Constants::MAIL_FROM_ADDRESS, Constants::MAIL_FROM_NAME);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $template;

        return $mail->send();
    }

    public function loadTemplate($template, $data = [], $baseUrl = '')
    {
        $token = $data['token'] ?? '';

        $url = Utils::getBaseUrl() . $baseUrl . "?token=$token";

        foreach ($data as $key => $value) {
            $variable = '{{' . strtoupper($key) . '}}';
            $template = str_replace($variable, $value, $template);
        }

        $template = str_replace('{{URL}}', $url, $template);

        return $template;
    }
}
