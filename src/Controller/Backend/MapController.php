<?php

namespace AcMarche\Bottin\Controller\Backend;

use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Location\Form\LocalisationType;
use AcMarche\Bottin\Repository\FicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Map controller.
 *
 * @Route("/backend/map")
 */
class MapController extends AbstractController
{
    private FicheRepository $ficheRepository;

    public function __construct(FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/{uuid}/edit", name="bottin_backend_map_edit", methods={"GET", "POST"})
     * @IsGranted("TOKEN_EDIT", subject="token")
     */
    public function edit(Token $token, Request $request): Response
    {
        $fiche = $token->getFiche();
        $form = $this->createForm(LocalisationType::class, $fiche);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ficheRepository->flush();
            $this->addFlash('success', 'La localisation a bien été modifiée');

            return $this->redirectToRoute('bottin_backend_fiche_show', ['uuid' => $token->getUuid()]);
        }

        return $this->render(
            '@AcMarcheBottin/backend/map/edit.html.twig',
            [
                'fiche' => $fiche,
                'token' => $token,
                'form' => $form->createView(),
            ]
        );
    }
}
