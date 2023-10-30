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
 */
#[Route(path: '/token')]
class TokenController extends AbstractController
{
    public function __construct(private readonly TokenUtils $tokenUtils)
    {
    }

    #[Route(path: '/log/{uuid}', name: 'bottin_front_token_show')]
    public function show(Request $request, Token $token): Response
    {
        if ($this->tokenUtils->isExpired($token)) {
            $this->addFlash('danger', 'Page expirée');

            return $this->redirectToRoute('bottin_front_home');
        }

        $fiche = $token->fiche;

        return $this->render(
            '@AcMarcheBottin/front/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
            ]
        );
    }
}
