<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class MailService 
{
    public function __construct(private MailerInterface $mailer, private string $fromEmail, private string $fromName) {}

    // Envio de correos con twig
    public function send(string $to, string $subject, string $template, array $context = []) {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        // MIRA ESTO: Vamos a forzar un log ANTES del envío
        error_log("MAIL SERVICE: Intentando enviar correo a " . $to . " desde " . $this->fromEmail);
        
        $this->mailer->send($email);
        
        // Y otro DESPUÉS
        error_log("MAIL SERVICE: Envío finalizado correctamente en Symfony.");
    }

    


        
}