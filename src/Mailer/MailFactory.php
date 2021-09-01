<?php

namespace AcMarche\Bottin\Mailer;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Utils\FicheUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailFactory
{
    private FicheUtils $ficheUtils;

    public function __construct(FicheUtils $ficheUtils)
    {
        $this->ficheUtils = $ficheUtils;
    }

    public function mailMessageToFiche(string $from, string $subject, string $message, Fiche $fiche): TemplatedEmail
    {
        $templatedEmail = (new TemplatedEmail())
            ->from($from)
            ->to('jf@marche.be')
            ->subject($subject)
            ->htmlTemplate('@AcMarcheBottin/mail/_fiche.html.twig')
            ->context(
                [
                    'message' => $message,
                    'fiche' => $fiche,
                    'action_url' => '',
                    'exception' => null,
                    'subject' => $subject,
                ]
            );

        return $templatedEmail;
    }

    public function mailContact(string $nom, string $from, string $message): TemplatedEmail
    {
        $templatedEmail = (new TemplatedEmail())
            ->subject('Contact depuis bottin marche')
            ->from($from)
            ->to('jf@marche.be')
            ->htmlTemplate('@AcMarcheBottin/mail/_frombottin.html.twig')
            ->context(
                [
                    'importance' => 'low',
                    'nom' => $nom,
                    'from' => $from,
                    'content' => $message,
                    'action_url' => '',
                    'exception' => null,
                    'subject' => 'Contact depuis bottin marche',
                ]
            );

        return $templatedEmail;
    }

    public function mailConfirmDemande(Fiche $fiche): TemplatedEmail
    {
        $emails = $this->ficheUtils->extractEmailsFromFiche($fiche);

        if (0 == \count($emails)) {
            throw new \Exception('Aucun email n\'a été trouvé pour ce commerçant');
        }

        $templatedEmail = new TemplatedEmail();
        $templatedEmail
            ->subject('Modification de vos coordonnées')
            ->from('adl@marche.be')
            ->to($emails[0])
            ->cc('adl@marche.be')
            ->htmlTemplate('@AcMarcheBottin/mail/_mail.html.twig')
            ->context(['fiche' => $fiche]);

        return $templatedEmail;
    }

    public function mailNewDemande(Fiche $fiche): TemplatedEmail
    {
        $templatedEmail = new TemplatedEmail();
        $templatedEmail
            ->subject('Cap: Une demande de modification de coordonnées')
            ->from('adl@marche.be')
            ->to('adl@marche.be')
            ->htmlTemplate('@AcMarcheBottin/mail/_new_demande.html.twig')
            ->context(['fiche' => $fiche]);

        return $templatedEmail;
    }

}
