<?php


namespace AcMarche\Bottin\Mailer;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Utils\FicheUtils;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailerBottin
{
    private FicheUtils $ficheUtils;
    private MailerInterface $mailer;

    public function __construct(FicheUtils $ficheUtils, MailerInterface $mailer)
    {
        $this->ficheUtils = $ficheUtils;
        $this->mailer = $mailer;
    }

    /**
     * @param Fiche $fiche
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function sendMailConfirmDemande(Fiche $fiche): void
    {
        $emails = $this->ficheUtils->extractEmailsFromFiche($fiche);

        if (0 == count($emails)) {
            throw new Exception('Aucun email n\'a été trouvé pour ce commerçant');
        }

        $templatedEmail = new TemplatedEmail();
        $templatedEmail
            ->subject('Modification de vos coordonnées')
            ->from('adl@marche.be')
            ->to($emails[0])
            ->cc('adl@marche.be')
            ->htmlTemplate('@AcMarcheBottin/demande/_mail.html.twig')
            ->context(['fiche' => $fiche]);

        $this->mailer->send($templatedEmail);
    }

    /**
     * @param Fiche $fiche
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function sendMailNewDemande(Fiche $fiche): void
    {
        $templatedEmail = new TemplatedEmail();
        $templatedEmail
            ->subject('Cap: Une demande de modification de coordonnées')
            ->from('adl@marche.be')
            ->to('adl@marche.be')
            ->textTemplate('@AcMarcheBottin/mail/_new_demande.html.twig')
            ->context(['fiche' => $fiche]);

        $this->mailer->send($templatedEmail);
    }
}
