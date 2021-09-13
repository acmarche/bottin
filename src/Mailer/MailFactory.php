<?php

namespace AcMarche\Bottin\Mailer;

use AcMarche\Bottin\Bottin;
use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use AcMarche\Bottin\Utils\FicheUtils;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class MailFactory
{
    private FicheUtils $ficheUtils;
    private ClassementHandler $classementHandler;
    private PdfFactory $pdfFactory;

    public function __construct(FicheUtils $ficheUtils, ClassementHandler $classementHandler, PdfFactory $pdfFactory)
    {
        $this->ficheUtils = $ficheUtils;
        $this->classementHandler = $classementHandler;
        $this->pdfFactory = $pdfFactory;
    }

    public function mailMessageToFiche(string $subject, string $body, Fiche $fiche): TemplatedEmail
    {
        $classements = $this->classementHandler->getClassements($fiche);
        $from = Bottin::EMAILS[Bottin::ECONOMIE];

        if (count($classements) > 0) {
            $fiche->root = $this->classementHandler->getRoot($classements[0]);
            $from = Bottin::EMAILS[$fiche->root];
        }

        $emails = $this->ficheUtils->extractEmailsFromFiche($fiche);
        $email = count($emails) > 0 ? $emails[0] : 'webmaster@marche.be';

        $templatedEmail = (new TemplatedEmail())
            ->from(new Address('adl@marche.be', $from))
            ->to(new Address('jf@marche.be', $email), new Address('adl@marche.be', $email))
            ->subject($subject)
            ->htmlTemplate('@AcMarcheBottin/mail/_fiche.html.twig')
            ->context(
                [
                    'body' => $body,
                    'importance' => 'high',
                    'fiche' => $fiche,
                    'action_url' => '',
                    'exception' => null,
                    'subject' => $subject,
                ]
            );

        $html = $this->pdfFactory->fiche($fiche);
        $invoicepdf = $this->pdfFactory->pdf->getOutputFromHtml($html);

        $templatedEmail->attach($invoicepdf, 'fiche_'.$fiche->getSlug().'.pdf', 'application/pdf');

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
                    'importance' => 'HIGH',
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
            ->htmlTemplate('@AcMarcheBottin/mail/_confirm_demande.html.twig')
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
