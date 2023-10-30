<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Document\Form\DocumentType;
use AcMarche\Bottin\Entity\Document;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Document controller.
 */
#[Route(path: '/admin/document')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class DocumentController extends AbstractController
{
    public function __construct(private readonly DocumentRepository $documentRepository)
    {
    }

    /**
     * Displays a form to create a new Document entity.
     */
    #[Route(path: '/new/{id}', name: 'bottin_admin_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Fiche $fiche): Response
    {
        $document = new Document($fiche);
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->documentRepository->persist($document);
            $this->documentRepository->flush();

            $this->addFlash('success', 'Le document a bien été créé');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/document/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Document entity.
     */
    #[Route(path: '/{id}', name: 'bottin_admin_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/document/show.html.twig',
            [
                'document' => $document,
                'fiche' => $document->fiche,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Document entity.
     */
    #[Route(path: '/{id}/edit', name: 'bottin_admin_document_edit', methods: ['GET', 'POST'])]
    public function edit(Document $document, Request $request): Response
    {
        $editForm = $this->createForm(DocumentType::class, $document);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->documentRepository->flush();
            $this->addFlash('success', 'Le document a bien été modifié');

            return $this->redirectToRoute('bottin_admin_document_show', ['id' => $document->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/document/edit.html.twig',
            [
                'document' => $document,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document): RedirectResponse
    {
        $fiche = $document->fiche;
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $this->documentRepository->remove($document);
            $this->documentRepository->flush();
            $this->addFlash('success', 'Le document a bien été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
    }
}
