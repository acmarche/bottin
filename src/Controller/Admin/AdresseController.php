<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Adresse\Form\AdresseType;
use AcMarche\Bottin\Adresse\Message\AdresseCreated;
use AcMarche\Bottin\Adresse\Message\AdresseDeleted;
use AcMarche\Bottin\Adresse\Message\AdresseUpdated;
use AcMarche\Bottin\Entity\Adresse;
use AcMarche\Bottin\Location\Form\LocalisationType;
use AcMarche\Bottin\Repository\AdresseRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/adresse')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class AdresseController extends AbstractController
{
    public function __construct(private AdresseRepository $adresseRepository, private MessageBusInterface $messageBus)
    {
    }

    #[Route(path: '/', name: 'bottin_admin_adresse', methods: ['GET'])]
    public function index(): Response
    {
        $adresses = $this->adresseRepository->findAll();

        return $this->render(
            '@AcMarcheBottin/admin/adresse/index.html.twig',
            [
                'adresses' => $adresses,
            ]
        );
    }

    #[Route(path: '/new', name: 'bottin_admin_adresse_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $adresse = new Adresse();
        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->adresseRepository->persist($adresse);
            $this->adresseRepository->flush();
            $this->messageBus->dispatch(new AdresseCreated($adresse->getId()));

            $this->addFlash('success', 'L\'adresse a bien été crée');

            return $this->redirectToRoute('bottin_admin_adresse_show', ['id' => $adresse->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/adresse/new.html.twig',
            [
                'entity' => $adresse,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_adresse_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Adresse $adresse): Response
    {
        $fiches = $adresse->getFiches();
        $form = $this->createForm(LocalisationType::class, $adresse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->adresseRepository->flush();

            $this->addFlash('success', 'La géolocalisation a bien été modifiée');

            return $this->redirectToRoute('bottin_admin_adresse_show', ['id' => $adresse->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/adresse/show.html.twig',
            [
                'adresse' => $adresse,
                'fiches' => $fiches,
                'form' => $form->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_adresse_edit', methods: ['GET', 'POST'])]
    public function edit(Adresse $adresse, Request $request): Response
    {
        $oldRue = $adresse->getRue();
        $editForm = $this->createForm(AdresseType::class, $adresse);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->adresseRepository->flush();

            $this->messageBus->dispatch(new AdresseUpdated($adresse->getId(), $oldRue));
            $this->addFlash('success', 'L\'adresse a bien été modifiée');

            return $this->redirectToRoute('bottin_admin_adresse_show', ['id' => $adresse->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/adresse/edit.html.twig',
            [
                'adresse' => $adresse,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}/delete', name: 'bottin_admin_adresse_delete', methods: ['POST'])]
    public function delete(Request $request, Adresse $adresse): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $this->messageBus->dispatch(new AdresseDeleted($adresse->getId()));
            $this->adresseRepository->remove($adresse);
            $this->adresseRepository->flush();
            $this->addFlash('success', "L'adresse a bien été supprimée");
        }

        return $this->redirectToRoute('bottin_admin_adresse');
    }
}
