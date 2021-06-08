<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController.
 *
 *  @Route("/token")
 */
class TokenController extends AbstractController
{
    private FicheRepository $ficheRepository;

    private TokenRepository $tokenRepository;

    public function __construct(
        FicheRepository $ficheRepository,
        TokenRepository $tokenRepository
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @Route("/generate", name="bottin_uuid_generate")
     */
    public function uuid(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();

        foreach ($fiches as $fiche) {
            if (!$token = $fiche->getToken()) {
                $token = new Token($fiche);
                $this->tokenRepository->persist($token);
            }
        }
        $this->tokenRepository->flush();

        return $this->render(
            '@AcMarcheBottin/default/uuid.html.twig',
            ['fiches' => $fiches]
        );
    }
}
