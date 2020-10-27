<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\MapType;
use AcMarche\Bottin\Repository\FicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Map controller.
 *
 * @Route("/map")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class MapController extends AbstractController
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct( FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * Displays a form to edit an existing Map entity.
     *
     * @Route("/{id}/edit", name="bottin_map_edit", methods={"GET", "POST"})
     */
    public function edit(Fiche $fiche, Request $request)
    {
        if ($fiche->getFtlb()) {
            $this->addFlash('warning', 'Vous ne pouvez pas éditer cette fiche car elle provient de la ftlb');

            return $this->redirectToRoute('bottin_fiche_show', ['id' => $fiche->getId()]);
        }
        if (!$fiche->getLatitude() && !$fiche->getLongitude()) {
            try {
                $this->geolocalisationService->convertToCoordonate($fiche, false);
            } catch (\Exception $e) {
                $this->addFlash('danger', "La latitude et longitude n'ont pas pu être trouvées: ".$e->getMessage());
            }
        }

        $form = $this->createForm(MapType::class, $fiche);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ficheRepository->flush();
            $this->addFlash('success', 'La situation a bien été modifiée');

            return $this->redirectToRoute('bottin_fiche_show', ['id' => $fiche->getId()]);
        }

        $key = $this->getParameter('bottin.api_key');

        return $this->render(
            '@AcMarcheBottin/map/edit.html.twig',
            [
                'fiche' => $fiche,
                'key' => $key,
                'form' => $form->createView(),
            ]
        );
    }
}
