<?php


namespace AcMarche\Bottin\Service;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Utils\FicheUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerBottin
{
    /**
     * @var FicheUtils
     */
    private $ficheUtils;
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(FicheUtils $ficheUtils, MailerInterface $mailer)
    {
        $this->ficheUtils = $ficheUtils;
        $this->mailer = $mailer;
    }

    /**
     * @param Fiche $fiche
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Exception
     */
    public function sendMailConfirmDemande(Fiche $fiche)
    {
        $emails = $this->ficheUtils->extractEmailsFromFiche($fiche);

        if (0 == count($emails)) {
            throw new \Exception('Aucun email n\'a été trouvé pour ce commerçant');
        }

        $message = new TemplatedEmail();
        $message
            ->subject('Modification de vos coordonnées')
            ->from('adl@marche.be')
            ->to($emails[0])
            ->cc('adl@marche.be')
            ->htmlTemplate('@AcMarcheBottin/demande/_mail.html.twig')
            ->context(['fiche' => $fiche]);

        $this->mailer->send($message);
    }

    /**
     * @param Fiche $fiche
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Exception
     */
    public function sendMailNewDemande(Fiche $fiche)
    {
        $message = new TemplatedEmail();
        $message
            ->subject('Cap: Une demande de modification de coordonnées')
            ->from('adl@marche.be')
            ->to('adl@marche.be')
            ->textTemplate('@AcMarcheBottin/mail/_new_demande.html.twig')
            ->context(['fiche' => $fiche]);

        $this->mailer->send($message);
    }

}
