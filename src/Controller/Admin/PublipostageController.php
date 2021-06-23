<?php

namespace AcMarche\Bottin\Controller\Admin;

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

    private FicheRepository $ficheRepository;

    public function __construct(Mailer $mailer, FicheRepository $ficheRepository)
    {
        $this->mailer = $mailer;
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/categories", name="bottin_admin_publipostage", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(MessageType::class, ['from' => $this->getParameter('bottin.email_from')]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $fiches = $this->ficheRepository->findAllWithJoins();
            $fiche = $fiches[rand(0, 1000)];
            $this->mailer->sendMessage($data['from'], $data['subject'], $data['message'], $fiche);
            $this->addFlash('success', 'Message envoyé');
        }

        return $this->render(
            '@AcMarcheBottin/admin/publipostage/index.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
