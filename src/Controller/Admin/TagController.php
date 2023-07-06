<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Tag;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Tag\Form\TagType;
use AcMarche\Bottin\Tag\Repository\TagRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/tag')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class TagController extends AbstractController
{
    public function __construct(private TagRepository $tagRepository, private FicheRepository $ficheRepository)
    {
    }

    #[Route(path: '/', name: 'bottin_admin_tag', methods: ['GET'])]
    public function index(): Response
    {
        $tags = $this->tagRepository->findAllOrdered();

        return $this->render(
            '@AcMarcheBottin/admin/tag/index.html.twig',
            [
                'tags' => $tags,
            ]
        );
    }

    #[Route(path: '/new', name: 'bottin_admin_tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagRepository->persist($tag);
            $this->tagRepository->flush();

            $this->addFlash('success', 'Le point de vente a bien été créé');

            return $this->redirectToRoute('bottin_admin_tag');
        }

        return $this->render(
            '@AcMarcheBottin/admin/tag/new.html.twig',
            [
                'entity' => $tag,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_tag_show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        $fiches = $this->ficheRepository->findByTag($tag);

        return $this->render(
            '@AcMarcheBottin/admin/tag/show.html.twig',
            [
                'tag' => $tag,
                'fiches' => $fiches,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_tag_edit', methods: ['GET', 'POST'])]
    public function edit(Tag $tag, Request $request): Response
    {
        $editForm = $this->createForm(TagType::class, $tag);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->tagRepository->flush();
            $this->addFlash('success', 'Le point de vente a bien été modifié');

            return $this->redirectToRoute('bottin_admin_tag');
        }

        return $this->render(
            '@AcMarcheBottin/admin/tag/edit.html.twig',
            [
                'tag' => $tag,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_tag_delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete' . $tag->getId(), $request->request->get('_token'))) {
            $this->tagRepository->remove($tag);
            $this->tagRepository->flush();
            $this->addFlash('success', 'Le point de vente a bien été supprimé');
        }

        return $this->redirectToRoute('bottin_admin_tag');
    }
}
