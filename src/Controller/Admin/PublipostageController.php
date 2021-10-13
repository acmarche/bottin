<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Export\ExportUtils;
use AcMarche\Bottin\Form\MessageType;
use AcMarche\Bottin\Mailer\MailFactory;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use AcMarche\Bottin\Utils\FicheUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Publipostage controller.
 *
 * @Route("/admin/publipostage")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class PublipostageController extends AbstractController
{
    private MailerInterface $mailer;
    private ExportUtils $exportUtils;
    private MailFactory $mailFactory;
    private FicheUtils $ficheUtils;
    private PdfFactory $pdfFactory;

    public function __construct(
        MailerInterface $mailer,
        MailFactory $mailFactory,
        ExportUtils $exportUtils,
        FicheUtils $ficheUtils,
        PdfFactory $pdfFactory
    ) {
        $this->mailer = $mailer;
        $this->exportUtils = $exportUtils;
        $this->mailFactory = $mailFactory;
        $this->ficheUtils = $ficheUtils;
        $this->pdfFactory = $pdfFactory;
    }

    /**
     * @Route("/", name="bottin_admin_publipostage_index", methods={"GET"})
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());

        return $this->render(
            '@AcMarcheBottin/admin/publipostage/index.html.twig',
            [
                'fiches' => $fiches,
            ]
        );
    }

    /**
     * @Route("/mail", name="bottin_admin_publipostage_mail", methods={"GET", "POST"})
     */
    public function byMail(Request $request): Response
    {
        $form = $this->createForm(MessageType::class, ['from' => $this->getParameter('bottin.email_from')]);
        $user = $this->getUser();
        $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $i = 0;
            foreach ($fiches as $fiche) {
                $message = $data['message'];
                $message = $this->exportUtils->replaceUrlToken($fiche, $message);
                $email = $this->mailFactory->mailMessageToFiche($data['subject'], $message, $fiche);
                try {
                    $this->mailer->send($email);
                    $this->addFlash('success', 'Votre message a bien été envoyé');
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'envoie du message: '.$e->getMessage());
                }
                if (5 == $i) {
                    break;
                }
                ++$i;
            }

            return $this->redirectToRoute('bottin_admin_publipostage_index');
        }

        $noEmails = [];
        foreach ($fiches as $fiche) {
            if (0 == \count($this->ficheUtils->extractEmailsFromFiche($fiche))) {
                $noEmails[] = $fiche;
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/publipostage/mail.html.twig',
            [
                'form' => $form->createView(),
                'fiches' => $fiches,
                'noEmails' => $noEmails,
            ]
        );
    }

    /**
     * @Route("/paper/{id}", name="bottin_admin_publipostage_paper", methods={"GET", "POST"})
     */
    public function byPaper(Fiche $fiche = null): Response
    {
        if ($fiche) {
            $fiches = [$fiche];
        } else {
            $user = $this->getUser();
            $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());
        }

        $html = $this->pdfFactory->fichesPublipostage($fiches);

          return new Response($html);

        return $this->pdfFactory->sendResponse($html, 'Fiches-publipostage');
    }
}
