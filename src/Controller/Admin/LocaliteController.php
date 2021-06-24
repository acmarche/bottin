<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Localite;
use AcMarche\Bottin\Form\LocaliteType;
use AcMarche\Bottin\Localite\Message\LocaliteCreated;
use AcMarche\Bottin\Localite\Message\LocaliteDeleted;
use AcMarche\Bottin\Localite\Message\LocaliteUpdated;
use AcMarche\Bottin\Repository\LocaliteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lieu controller.
 *
 * @Route("/admin/localite")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class LocaliteController extends AbstractController
{
    private LocaliteRepository $localiteRepository;

    public function __construct(LocaliteRepository $localiteRepository)
    {
        $this->localiteRepository = $localiteRepository;
    }

    /**
     * Lists all Lieu entities.
     *
     * @Route("/", name="bottin_admin_localite_index", methods={"GET"})
     */
    public function index(): Response
    {
        $localites = $this->localiteRepository->findAll();

        return $this->render(
            '@AcMarcheBottin/admin/localite/index.html.twig',
            [
                'localites' => $localites,
            ]
        );
    }

    /**
     * Displays a form to create a new Lieu entity.
     *
     * @Route("/new", name="bottin_admin_localite_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $localite = new Localite();

        $form = $this->createForm(LocaliteType::class, $localite);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->localiteRepository->persist($localite);
            $this->localiteRepository->flush();
            $this->dispatchMessage(new LocaliteCreated($localite->getId()));

            return $this->redirectToRoute('bottin_admin_localite_show', ['id' => $localite->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/localite/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Lieu entity.
     *
     * @Route("/{id}", name="bottin_admin_localite_show", methods={"GET","POST"})
     */
    public function show(Request $request, Localite $localite): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/localite/show.html.twig',
            [
                'localite' => $localite,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Lieu entity.
     *
     * @Route("/{id}/edit", name="bottin_admin_localite_edit", methods={"GET", "POST"})
     */
    public function edit(Localite $localite, Request $request): Response
    {
        $editForm = $this->createForm(LocaliteType::class, $localite);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->localiteRepository->flush();

            $this->dispatchMessage(new LocaliteUpdated($localite->getId()));

            return $this->redirectToRoute('bottin_admin_localite_show', ['id' => $localite->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/localite/edit.html.twig',
            [
                'localite' => $localite,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_admin_localite_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Localite $localite): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$localite->getId(), $request->request->get('_token'))) {
            $this->dispatchMessage(new LocaliteDeleted($localite->getId()));
            $this->localiteRepository->remove($localite);
            $this->localiteRepository->flush();
        }

        return $this->redirectToRoute('bottin_admin_localite_index');
    }
}
