<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\MetaField;
use AcMarche\Bottin\Entity\Pdv;
use AcMarche\Bottin\Form\PdvType;
use AcMarche\Bottin\Meta\Form\MetaFieldType;
use AcMarche\Bottin\Meta\Repository\MetaFieldRepository;
use AcMarche\Bottin\Repository\PdvRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/meta/field')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class MetaFieldController extends AbstractController
{
    public function __construct(private readonly MetaFieldRepository $metaFieldRepository)
    {
    }

    #[Route(path: '/', name: 'bottin_admin_meta_field', methods: ['GET'])]
    public function index(): Response
    {
        $meta_fields = $this->metaFieldRepository->findAll();

        return $this->render(
            '@AcMarcheBottin/admin/meta_field/index.html.twig',
            [
                'meta_fields' => $meta_fields,
            ]
        );
    }

    #[Route(path: '/new', name: 'bottin_admin_meta_field_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $meta_field = new MetaField();
        $form = $this->createForm(MetaFieldType::class, $meta_field);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->metaFieldRepository->persist($meta_field);
            $this->metaFieldRepository->flush();

            $this->addFlash('success', 'Le champ bien été créé');

            return $this->redirectToRoute('bottin_admin_meta_field');
        }

        return $this->render(
            '@AcMarcheBottin/admin/meta_field/new.html.twig',
            [
                'entity' => $meta_field,
                'form' => $form->createView(),
            ]
        );
    }
    
    #[Route(path: '/{id}', name: 'bottin_admin_meta_field_show', methods: ['GET'])]
    public function show(MetaField $meta_field): Response
    {
        $fiches = $meta_field->getFiches();

        return $this->render(
            '@AcMarcheBottin/admin/meta_field/show.html.twig',
            [
                'meta_field' => $meta_field,
                'fiches' => $fiches,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_meta_field_edit', methods: ['GET', 'POST'])]
    public function edit(MetaField $meta_field, Request $request): Response
    {
        $editForm = $this->createForm(PdvType::class, $meta_field);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->metaFieldRepository->flush();
            $this->addFlash('success', 'Le point de vente a bien été modifié');

            return $this->redirectToRoute('bottin_admin_meta_field');
        }

        return $this->render(
            '@AcMarcheBottin/admin/meta_field/edit.html.twig',
            [
                'meta_field' => $meta_field,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_meta_field_delete', methods: ['POST'])]
    public function delete(Request $request, MetaField $meta_field): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$meta_field->getId(), $request->request->get('_token'))) {
            $this->meta_fieldRepository->remove($meta_field);
            $this->meta_fieldRepository->flush();
            $this->addFlash('success', 'Le point de vente a bien été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_meta_field');
    }
}
