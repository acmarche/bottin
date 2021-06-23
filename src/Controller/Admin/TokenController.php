<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Token\TokenUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController.
 *
 * @Route("/admin/token")
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
     * @Route("/generate", name="bottin_admin_token_generate_for_all")
     */
    public function uuid(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $this->tokenUtils->generateForAll();

        return $this->render(
            '@AcMarcheBottin/admin/default/uuid.html.twig',
            ['fiches' => $fiches]
        );
    }

    /**
     * @Route("/log/{uuid}",name="bottin_admin_token_show")
     */
    public function show(Request $request, Token $token): Response
    {
        if ($this->tokenUtils->isExpired($token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_home');
        }
        $fiche = $token->getFiche();

        return $this->render(
            '@AcMarcheBottin/admin/default/index.html.twig',
            [
                'fiche' => $fiche,
            ]
        );
    }
}
