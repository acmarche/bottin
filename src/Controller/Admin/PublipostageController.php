<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Export\ExportUtils;
use AcMarche\Bottin\Form\MessageType;
use AcMarche\Bottin\Mailer\MailFactory;
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

    public function __construct(MailerInterface $mailer, MailFactory $mailFactory, ExportUtils $exportUtils)
    {
        $this->mailer = $mailer;
        $this->exportUtils = $exportUtils;
        $this->mailFactory = $mailFactory;
    }

    /**
     * @Route("/", name="bottin_admin_publipostage", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(MessageType::class, ['from' => $this->getParameter('bottin.email_from')]);
        $user = $this->getUser();
        $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            foreach ($fiches as $fiche) {
                $message = $data['message'];
                $message = $this->exportUtils->replaceUrlToken($fiche, $message);
                $email = $this->mailFactory->mailMessageToFiche($data['from'], $data['subject'], $message, $fiche);
                try {
                    $this->mailer->send($email);
                    $this->addFlash('success', 'Votre message a bien été envoyé');
                } catch (TransportExceptionInterface $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'envoie du message: '.$e->getMessage());
                }
                break;
            }
            $email = $this->mailFactory->mailMessageToFiche($data['from'], $data['subject'], $data['message'], $fiche);
            try {
                $this->mailer->send($email);
                $this->addFlash('success', 'Votre message a bien été envoyé');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('danger', 'Erreur lors de l\'envoie du message: '.$e->getMessage());
            }

            return $this->redirectToRoute('bottin_admin_publipostage');
        }

        return $this->render(
            '@AcMarcheBottin/admin/publipostage/index.html.twig',
            [
                'form' => $form->createView(),
                'fiches' => $fiches,
            ]
        );
    }
}
