<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Pdv;
use AcMarche\Bottin\Form\PdvType;
use AcMarche\Bottin\Repository\PdvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/admin/pdv')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class PdvController extends AbstractController
{
    public function __construct(private readonly PdvRepository $pdvRepository)
    {
    }

    #[Route(path: '/', name: 'bottin_admin_pdv', methods: ['GET'])]
    public function index(): Response
    {
        $pdvs = $this->pdvRepository->findAll();

        return $this->render(
            '@AcMarcheBottin/admin/pdv/index.html.twig',
            [
                'pdvs' => $pdvs,
            ]
        );
    }

    #[Route(path: '/new', name: 'bottin_admin_pdv_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $pdv = new Pdv();
        $form = $this->createForm(PdvType::class, $pdv);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pdvRepository->persist($pdv);
            $this->pdvRepository->flush();

            $this->addFlash('success', 'Le point de vente a bien été créé');

            return $this->redirectToRoute('bottin_admin_pdv');
        }

        return $this->render(
            '@AcMarcheBottin/admin/pdv/new.html.twig',
            [
                'entity' => $pdv,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_pdv_show', methods: ['GET'])]
    public function show(Pdv $pdv): Response
    {
        $fiches = $pdv->fiches;

        return $this->render(
            '@AcMarcheBottin/admin/pdv/show.html.twig',
            [
                'pdv' => $pdv,
                'fiches' => $fiches,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_pdv_edit', methods: ['GET', 'POST'])]
    public function edit(Pdv $pdv, Request $request): Response
    {
        $editForm = $this->createForm(PdvType::class, $pdv);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->pdvRepository->flush();
            $this->addFlash('success', 'Le point de vente a bien été modifié');

            return $this->redirectToRoute('bottin_admin_pdv');
        }

        return $this->render(
            '@AcMarcheBottin/admin/pdv/edit.html.twig',
            [
                'pdv' => $pdv,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_pdv_delete', methods: ['POST'])]
    public function delete(Request $request, Pdv $pdv): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$pdv->getId(), $request->request->get('_token'))) {
            $this->pdvRepository->remove($pdv);
            $this->pdvRepository->flush();
            $this->addFlash('success', 'Le point de vente a bien été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_pdv');
    }
}
