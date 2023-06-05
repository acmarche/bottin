<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Location\Form\LocalisationType;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Map controller.
 */
#[Route(path: '/admin/map')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class MapController extends AbstractController
{
    public function __construct(private FicheRepository $ficheRepository)
    {
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_map_edit', methods: ['GET', 'POST'])]
    public function edit(Fiche $fiche, Request $request): Response
    {
        if ($fiche->getFtlb()) {
            $this->addFlash('warning', 'Vous ne pouvez pas éditer cette fiche car elle provient de la ftlb');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }
        $form = $this->createForm(LocalisationType::class, $fiche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ficheRepository->flush();
            $this->addFlash('success', 'La localisation a bien été modifiée');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/map/edit.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }
}
