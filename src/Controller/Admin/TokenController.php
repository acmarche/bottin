<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Token\TokenUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController.
 *
 * @Route("/admin/token")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class TokenController extends AbstractController
{
    private TokenUtils $tokenUtils;
    private FicheRepository $ficheRepository;

    public function __construct(
        TokenUtils $tokenUtils,
        FicheRepository $ficheRepository
    ) {
        $this->tokenUtils = $tokenUtils;
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/generate/all", name="bottin_admin_token_generate_for_all", methods={"GET", "POST"})
     */
    public function generateAll(Request $request): Response
    {
        $form = $this->createFormBuilder()->getForm();

        $fiches = $this->ficheRepository->findAllWithJoins();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tokenUtils->generateForAll();
            $this->addFlash('success', 'Tokens générés');

            return $this->redirectToRoute('bottin_admin_token_generate_for_all');
        }

        return $this->render(
            '@AcMarcheBottin/admin/default/uuid.html.twig',
            [
                'fiches' => $fiches,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/generate/one/{id}", name="bottin_admin_token_generate_for_one")
     */
    public function generateOne(Fiche $fiche): Response
    {
        $this->tokenUtils->generateForOneFiche($fiche, true);
        $this->addFlash('success', 'Token généré');

        return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
    }
}
