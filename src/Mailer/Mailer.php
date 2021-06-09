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

    public function create(Fiche $message): TemplatedEmail
    {
        $templatedEmail = (new TemplatedEmail())
            ->subject($message->getSujet())
            ->from($message->getFrom())
            //  ->htmlTemplate('@AcMarcheMercrediAdmin/mail/mail.html.twig')
            ->textTemplate('@AcMarcheMercrediAdmin/message/_mail.txt.twig')
            ->context(
                [
                    'texte' => $message->getTexte(),
                    'organisation' => $this->organisation,
                ]
            );

        /*
         * Pieces jointes.
         */
        if (null !== ($uploadedFile = $message->getFile())) {
            $templatedEmail->attachFromPath(
                $uploadedFile->getRealPath(),
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getClientMimeType()
            );
        }

        return $templatedEmail;
    }
}
