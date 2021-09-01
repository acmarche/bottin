<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Token\TokenUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/generate/all", name="bottin_admin_token_generate_for_all")
     */
    public function generateAll(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $this->tokenUtils->generateForAll();

        return $this->render(
            '@AcMarcheBottin/admin/default/uuid.html.twig',
            ['fiches' => $fiches]
        );
    }

    /**
     * @Route("/generate/one/{id}", name="bottin_admin_token_generate_for_one")
     */
    public function generateOne(Fiche $fiche): Response
    {
        $this->tokenUtils->generateForOneFiche($fiche, true);

        return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
    }
}
