<?php

namespace AcMarche\Bottin\Controller\Front;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Token\TokenUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController.
 *
 * @Route("/token")
 */
class TokenController extends AbstractController
{
    private TokenUtils $tokenUtils;

    public function __construct(
        TokenUtils $tokenUtils
    ) {
        $this->tokenUtils = $tokenUtils;
    }

    /**
     * @Route("/log/{uuid}",name="bottin_token_show")
     */
    public function show(Request $request, Token $token): Response
    {
        if ($this->tokenUtils->isExpired($token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_home');
        }

        $fiche = $token->getFiche();

        return $this->render(
            '@AcMarcheBottin/front/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
            ]
        );
    }
}
