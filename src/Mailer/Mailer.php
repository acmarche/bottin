<?php

namespace AcMarche\Bottin\Mailer;

use AcMarche\Bottin\Entity\Fiche;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class Mailer
{
    use InitMailerTrait;

    public function sendMessage(string $from, string $subject, string $message, Fiche $fiche): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from($from)
            ->to('jf@marche.be')
            ->subject($subject)
            ->textTemplate('@AcMarcheBottin/admin/mail/_fiche.txt.twig')
            ->context(
                [
                    'message' => $message,
                    'fiche' => $fiche,
                ]
            );

        $this->sendMail($templatedEmail);
    }

    /**
     * @param string $nom
     * @param string $from
     * @param string $message
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendContact(string $nom, string $from, string $message): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->subject('Contact depuis bottin marche')
            ->from($from)
            ->to('jf@marche.be')
            ->textTemplate('@AcMarcheBottin/backend/mail/_frombottin.txt.twig')
            ->context(
                [
                    'nom' => $nom,
                    'from' => $from,
                    'content' => $message,
                ]
            );
        $this->sendMail($templatedEmail);
    }
}
