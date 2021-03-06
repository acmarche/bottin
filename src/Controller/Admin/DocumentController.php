<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Document;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\DocumentEditType;
use AcMarche\Bottin\Form\DocumentType;
use AcMarche\Bottin\Repository\DocumentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Document controller.
 *
 * @Route("/admin/document")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class DocumentController extends AbstractController
{
    private DocumentRepository $documentRepository;

    public function __construct(DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Displays a form to create a new Document entity.
     *
     * @Route("/new/{id}", name="bottin_admin_document_new", methods={"GET", "POST"})
     */
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
     *
     * @Route("/{id}", name="bottin_admin_document_show", methods={"GET"})
     */
    public function show(Document $document): Response
    {
        return $this->render(
            '@AcMarcheBottin/admin/document/show.html.twig',
            [
                'document' => $document,
                'fiche' => $document->getFiche(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Document entity.
     *
     * @Route("/{id}/edit", name="bottin_admin_document_edit", methods={"GET", "POST"})
     */
    public function edit(Document $document, Request $request): Response
    {
        $editForm = $this->createForm(DocumentEditType::class, $document);

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

    /**
     * @Route("/{id}", name="bottin_admin_document_delete", methods={"POST"})
     */
    public function delete(Request $request, Document $document): RedirectResponse
    {
        $fiche = $document->getFiche();
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $this->documentRepository->remove($document);
            $this->documentRepository->flush();
            $this->addFlash('success', 'Le document a bien été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
    }
}
