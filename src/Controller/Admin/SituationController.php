<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Situation;
use AcMarche\Bottin\Form\SituationType;
use AcMarche\Bottin\Repository\SituationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Situation controller.
 *
 * @Route("/admin/situation")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class SituationController extends AbstractController
{
    private SituationRepository $situationRepository;

    public function __construct(SituationRepository $situationRepository)
    {
        $this->situationRepository = $situationRepository;
    }

    /**
     * Lists all Situation entities.
     *
     * @Route("/", name="bottin_admin_situation", methods={"GET"})
     */
    public function index(): Response
    {
        $situations = $this->situationRepository->findAll();

        return $this->render(
            '@AcMarcheBottin/admin/situation/index.html.twig',
            [
                'situations' => $situations,
            ]
        );
    }

    /**
     * Displays a form to create a new Situation entity.
     *
     * @Route("/new", name="bottin_admin_situation_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $situation = new Situation();

        $form = $this->createForm(SituationType::class, $situation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->situationRepository->persist($situation);
            $this->situationRepository->flush();

            $this->addFlash('success', 'La situation a bien été crée');

            return $this->redirectToRoute('bottin_admin_situation');
        }

        return $this->render(
            '@AcMarcheBottin/admin/situation/new.html.twig',
            [
                'entity' => $situation,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Situation entity.
     *
     * @Route("/{id}", name="bottin_admin_situation_show", methods={"GET"})
     */
    public function show(Situation $situation): Response
    {
        $fiches = $situation->getFiches();

        return $this->render(
            '@AcMarcheBottin/admin/situation/show.html.twig',
            [
                'situation' => $situation,
                'fiches' => $fiches,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Situation entity.
     *
     * @Route("/{id}/edit", name="bottin_admin_situation_edit", methods={"GET", "POST"})
     */
    public function edit(Situation $situation, Request $request): Response
    {
        $editForm = $this->createForm(SituationType::class, $situation);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->situationRepository->flush();
            $this->addFlash('success', 'La situation a bien été modifiée');

            return $this->redirectToRoute('bottin_admin_situation');
        }

        return $this->render(
            '@AcMarcheBottin/admin/situation/edit.html.twig',
            [
                'situation' => $situation,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_admin_situation_delete", methods={"POST"})
     */
    public function delete(Request $request, Situation $situation): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$situation->getId(), $request->request->get('_token'))) {
            $this->situationRepository->remove($situation);
            $this->situationRepository->flush();
            $this->addFlash('success', 'La situation a bien été supprimée');
        }

        return $this->redirectToRoute('bottin_admin_situation');
    }
}
