<?php

namespace App\Security;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;

class AuthCodeMailer implements AuthCodeMailerInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();


        $email = (new TemplatedEmail())
            ->from(new Address('info@tellaris.ch', 'App Tallaris'))
            ->to($user->getEmail())
            ->subject('Votre code d’authentification')
            ->htmlTemplate('emails/authCodeMailer.html.twig')
                // pass variables
            ->context([
                'auth_code' => $authCode,
            ]);

        $this->mailer->send($email);

        // Send email
    }
}
