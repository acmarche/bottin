<?php

namespace AcMarche\Bottin\Controller\Admin;

use Exception;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Export\ExportUtils;
use AcMarche\Bottin\Form\MessageType;
use AcMarche\Bottin\Mailer\MailFactory;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use AcMarche\Bottin\Utils\FicheUtils;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/publipostage')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class PublipostageController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly MailFactory $mailFactory,
        private readonly ExportUtils $exportUtils,
        private readonly FicheUtils $ficheUtils,
        private readonly PdfFactory $pdfFactory
    ) {
    }

    #[Route(path: '/', name: 'bottin_admin_publipostage_index', methods: ['GET'])]
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

    #[Route(path: '/mail', name: 'bottin_admin_publipostage_mail_all', methods: ['GET', 'POST'])]
    #[Route(path: '/mail/{id}', name: 'bottin_admin_publipostage_mail_fiche', methods: ['GET', 'POST'])]
    public function byMail(Request $request, Fiche $fiche = null): Response
    {
        $user = $this->getUser();
        $to = null;
        if ($fiche instanceof Fiche) {
            $fiches = [$fiche];
            $emails = $this->ficheUtils->extractEmailsFromFiche($fiche);
            $to = [] !== $emails ? $emails[0] : 'webmaster@marche.be';
        } else {
            $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());
        }

        $form = $this->createForm(MessageType::class, [
            'from' => $this->getParameter('bottin.email_from'),
            'to' => $to,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $i = 0;
            foreach ($fiches as $fiche) {
                $message = $data['message'];
                $to = $data['to'];
                $message = $this->exportUtils->replaceUrlToken($fiche, $message);
                $email = $this->mailFactory->mailMessageToFiche($to, $data['subject'], $message, $fiche);
                try {
                    $this->mailer->send($email);
                } catch (TransportExceptionInterface|Exception $e) {
                    $this->addFlash('danger', "Erreur lors de l'envoie du message: ".$e->getMessage());
                }

                if (15 == $i) {
                    break;
                }

                ++$i;
            }

            $this->addFlash('success', 'Les mails ont bien été envoyés');

            return $this->redirectToRoute('bottin_admin_publipostage_index');
        }

        $noEmails = [];
        foreach ($fiches as $fiche) {
            if (0 == \count($this->ficheUtils->extractEmailsFromFiche($fiche))) {
                $noEmails[] = $fiche;
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/publipostage/by_mail.html.twig',
            [
                'form' => $form->createView(),
                'fiches' => $fiches,
                'noEmails' => $noEmails,
            ]
        );
    }

    #[Route(path: '/paper', name: 'bottin_admin_publipostage_paper_all', methods: ['GET', 'POST'])]
    #[Route(path: '/paper/{id}', name: 'bottin_admin_publipostage_paper_fiche', methods: ['GET', 'POST'])]
    public function byPaper(Fiche $fiche = null): PdfResponse
    {
        if ($fiche instanceof Fiche) {
            $fiches = [$fiche];
        } else {
            $user = $this->getUser();
            $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());
        }

        $html = $this->pdfFactory->fichesPublipostage($fiches);

        //return new Response($html);
        return $this->pdfFactory->sendResponse($html, 'Fiches-publipostage');
    }
}
