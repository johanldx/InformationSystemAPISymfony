<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host = 'pro3.mail.ovh.net';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'quickbee@rootage.fr';
        $this->mailer->Password = 'password'; //modifié pour ne pas apparaitre sur github
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
    }

    public function sendEmail($to, $subject, $body, $from = 'quickbee@rootage.fr', $fromName = 'Quickbee')
    {
        try {
            $this->mailer->setFrom($from, $fromName);

            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }
}
