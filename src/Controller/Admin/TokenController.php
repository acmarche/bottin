<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Token\TokenUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TokenController.
 */
#[Route(path: '/admin/token')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class TokenController extends AbstractController
{
    public function __construct(private readonly TokenUtils $tokenUtils, private readonly FicheRepository $ficheRepository)
    {
    }

    #[Route(path: '/generate/all', name: 'bottin_admin_token_generate_for_all', methods: ['GET', 'POST'])]
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

    #[Route(path: '/generate/one/{id}', name: 'bottin_admin_token_generate_for_one')]
    public function generateOne(Fiche $fiche): RedirectResponse
    {
        $this->tokenUtils->generateForOneFiche($fiche, true);
        $this->addFlash('success', 'Token généré');

        return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
    }
}
