<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\MetaData;
use AcMarche\Bottin\Fiche\Form\FicheType;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Form\Search\SearchFicheType;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Horaire\Handler\HoraireService;
use AcMarche\Bottin\Meta\Repository\MetaDataRepository;
use AcMarche\Bottin\Meta\Repository\MetaFieldRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Utils\PathUtils;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/fiche')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class FicheController extends AbstractController
{
    public function __construct(
        private readonly PathUtils $pathUtils,
        private readonly ClassementRepository $classementRepository,
        private readonly FicheRepository $ficheRepository,
        private readonly HoraireService $horaireService,
        private readonly SearchEngineInterface $searchEngine,
        private readonly HistoryUtils $historyUtils,
        private readonly MetaFieldRepository $metaFieldRepository,
        private readonly MetaDataRepository $metaDataRepository,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'bottin_admin_fiche_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $args = [];
        $fiches = [];
        if ($session->has('fiche_search')) {
            $args = json_decode((string)$session->get('fiche_search'), true, 512, \JSON_THROW_ON_ERROR);
        }

        $form = $this->createForm(SearchFicheType::class, $args, ['method' => 'GET']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $session->set('fiche_search', json_encode($args, \JSON_THROW_ON_ERROR));

            try {
                $response = $this->searchEngine->doSearch($args['nom'], $args['localite']);
                $fiches = $response->getResults();
            } catch (BadRequest400Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/fiche/index.html.twig',
            [
                'search_form' => $form->createView(),
                'fiches' => $fiches,
            ]
        );
    }

    #[Route(path: '/new', name: 'bottin_admin_fiche_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        if ($this->isCsrfTokenValid('fichenew', $request->request->get('_token'))) {
            $params = $request->request;
            $societe = trim($params->get('societe'));

            if ('' === $societe || '0' === $societe) {
                $this->addFlash('danger', 'Le nom ne peut être vide');

                return $this->redirectToRoute('bottin_admin_fiche_new');
            }

            $fiche = new Fiche();
            $fiche->societe = $societe;
            $fiche->cp = $this->getParameter('bottin.cp_default');
            $this->ficheRepository->insert($fiche);
            $this->historyUtils->newFiche($fiche);

            $this->messageBus->dispatch(new FicheCreated($fiche->getId()));

            $this->addFlash('success', 'La fiche a bien ajoutée');
            $this->addFlash('success', 'Après avoir rempli les coordonnées, pensez à classer la fiche');

            return $this->redirectToRoute('bottin_admin_fiche_edit', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/fiche/new.html.twig',
            [
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_fiche_show', methods: ['GET'])]
    public function show(Fiche $fiche): Response
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        return $this->render(
            '@AcMarcheBottin/admin/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );
    }

    #[Route(path: '/{id}/edit', name: 'bottin_admin_fiche_edit', methods: ['GET', 'POST'])]
    public function edit(Fiche $fiche, Request $request): Response
    {
        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();
        $this->horaireService->initHoraires($fiche);

        $metas = [];
        foreach ($this->metaFieldRepository->findAll() as $field) {
            $value = $this->metaDataRepository->findOneByFicheAndName($fiche, $field->getSlug());
            $metas[] = new MetaData($fiche, $field->name, $value);
        }
        $fiche->metas = $metas;

        $editForm = $this->createForm(FicheType::class, $fiche);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();


            $horaires = $data->horaires;
            $this->horaireService->handleEdit($fiche, $horaires);

            try {
                $this->historyUtils->diffFiche($fiche);
            } catch (Exception) {
                $this->addFlash('danger', "Erreur pour l'enregistrement dans l' historique");
            }

            $this->ficheRepository->flush();
            $this->addFlash('success', 'La fiche a bien été modifiée');

            try {
                $this->messageBus->dispatch(new FicheUpdated($fiche->getId(), $oldAdresse));
            } catch (Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/fiche/edit.html.twig',
            [
                'fiche' => $fiche,
                'form' => $editForm->createView(),
            ]
        );
    }

    #[Route(path: '/{id}', name: 'bottin_admin_fiche_delete', methods: ['POST'])]
    public function delete(Request $request, Fiche $fiche): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$fiche->getId(), $request->request->get('_token'))) {
            $nomFiche = $fiche->societe;
            $id = $fiche->getId();
            $this->messageBus->dispatch(new FicheDeleted($id));
            $this->ficheRepository->remove($fiche);
            $this->ficheRepository->flush();

            $this->historyUtils->deleteFiche($nomFiche);
            $this->addFlash('success', 'La fiche a bien été supprimée');
        }

        return $this->redirectToRoute('bottin_admin_fiche_index');
    }
}
