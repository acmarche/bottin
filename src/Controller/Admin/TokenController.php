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
 * @Route("/token")
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
     * @Route("/generate", name="bottin_uuid_generate")
     */
    public function uuid(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();

        return $this->render(
            '@AcMarcheBottin/admin/default/uuid.html.twig',
            ['fiches' => $fiches]
        );
    }

    /**
     * @Route("/log/{uuid}",name="bottin_token_show")
     */
    public function show(Request $request, Token $token)
    {
        dump($this->tokenUtils->isExpired($token));

        return $this->render(
            '@AcMarcheBottin/admin/default/index.html.twig',
            ['categories' => []]
        );
    }
}
