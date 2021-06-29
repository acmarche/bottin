<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Export\ExportUtils;
use AcMarche\Bottin\Form\MessageType;
use AcMarche\Bottin\Mailer\Mailer;
use AcMarche\Bottin\Repository\FicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Publipostage controller.
 *
 * @Route("/admin/publipostage")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class PublipostageController extends AbstractController
{
    private Mailer $mailer;
    private ExportUtils $exportUtils;

    public function __construct(Mailer $mailer, ExportUtils $exportUtils)
    {
        $this->mailer = $mailer;
        $this->exportUtils = $exportUtils;
    }

    /**
     * @Route("/", name="bottin_admin_publipostage", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(MessageType::class, ['from' => $this->getParameter('bottin.email_from')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getUser();

            $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());

            foreach ($fiches as $fiche) {
                $message = $data['message'];
                $message = $this->exportUtils->replaceUrlToken($fiche, $message);
                $this->mailer->sendMessage($data['from'], $data['subject'], $message, $fiche);
                break;
            }
            $this->mailer->sendMessage($data['from'], $data['subject'], $data['message'], $fiche);
            $this->addFlash('success', 'Message envoyé');

            return $this->redirectToRoute('bottin_admin_publipostage');
        }

        return $this->render(
            '@AcMarcheBottin/admin/publipostage/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
