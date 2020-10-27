<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Adresse\Message\AdresseCreated;
use AcMarche\Bottin\Adresse\Message\AdresseDeleted;
use AcMarche\Bottin\Adresse\Message\AdresseUpdated;
use AcMarche\Bottin\Entity\Adresse;
use AcMarche\Bottin\Form\AdresseType;
use AcMarche\Bottin\Form\LocalisationType;
use AcMarche\Bottin\Location\LocationUpdater;
use AcMarche\Bottin\Repository\AdresseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Lieu controller.
 *
 * @Route("/adresse")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class AdresseController extends AbstractController
{
    /**
     * @var AdresseRepository
     */
    private $adresseRepository;
    /**
     * @var LocationUpdater
     */
    private $locationUpdater;

    public function __construct(AdresseRepository $adresseRepository, LocationUpdater $locationUpdater)
    {
        $this->adresseRepository = $adresseRepository;
        $this->locationUpdater = $locationUpdater;
    }

    /**
     * Lists all Lieu entities.
     *
     * @Route("/", name="bottin_adresse", methods={"GET"})
     */
    public function index()
    {
        $adresses = $this->adresseRepository->findAll();

        return $this->render(
            '@AcMarcheBottin/adresse/index.html.twig',
            [
                'adresses' => $adresses,
            ]
        );
    }

    /**
     * Displays a form to create a new Lieu entity.
     *
     * @Route("/new", name="bottin_adresse_new", methods={"GET", "POST"})
     */
    public function new(Request $request)
    {
        $adresse = new Adresse();

        $form = $this->createForm(AdresseType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adresseRepository->persist($adresse);
            $this->adresseRepository->flush();
            $this->dispatchMessage(new AdresseCreated($adresse->getId()));

            $this->addFlash('success', 'L\'adresse a bien été crée');

            return $this->redirectToRoute('bottin_adresse');
        }

        return $this->render(
            '@AcMarcheBottin/adresse/new.html.twig',
            [
                'entity' => $adresse,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Lieu entity.
     *
     * @Route("/{id}", name="bottin_adresse_show", methods={"GET","POST"})
     */
    public function show(Request $request, Adresse $adresse)
    {
        $fiches = $adresse->getFiches();
        $form = $this->createForm(LocalisationType::class, $adresse);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->adresseRepository->flush();

            $this->addFlash("success", "La géolocalisation a bien été modifiée");
        }

        return $this->render(
            '@AcMarcheBottin/adresse/show.html.twig',
            [
                'adresse' => $adresse,
                'fiches' => $fiches,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Displays a form to edit an existing Lieu entity.
     *
     * @Route("/{id}/edit", name="bottin_adresse_edit", methods={"GET", "POST"})
     */
    public function edit(Adresse $adresse, Request $request)
    {
        $oldRue = $adresse->getRue();
        $editForm = $this->createForm(AdresseType::class, $adresse);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->adresseRepository->flush();

            $this->dispatchMessage(new AdresseUpdated($adresse->getId(), $oldRue));
            $this->addFlash('success', 'L\'adresse a bien été modifiée');

            return $this->redirectToRoute('bottin_adresse_show', ['id' => $adresse->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/adresse/edit.html.twig',
            [
                'adresse' => $adresse,
                'form' => $editForm->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="bottin_adresse_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Adresse $adresse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $this->dispatchMessage(new AdresseDeleted($adresse->getId()));
            $this->adresseRepository->remove($adresse);
            $this->adresseRepository->flush();
            $this->addFlash('success', "L'adresse a bien été supprimée");
        }

        return $this->redirectToRoute('bottin_adresse');
    }

}
