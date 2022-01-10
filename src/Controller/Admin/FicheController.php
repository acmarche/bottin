<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Fiche\Form\FicheType;
use AcMarche\Bottin\Fiche\Message\FicheCreated;
use AcMarche\Bottin\Fiche\Message\FicheDeleted;
use AcMarche\Bottin\Fiche\Message\FicheUpdated;
use AcMarche\Bottin\Form\Search\SearchFicheType;
use AcMarche\Bottin\History\HistoryUtils;
use AcMarche\Bottin\Horaire\Handler\HoraireService;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Search\SearchEngineInterface;
use AcMarche\Bottin\Utils\PathUtils;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Fiche controller.
 */
#[Route(path: '/admin/fiche')]
#[IsGranted(data: 'ROLE_BOTTIN_ADMIN')]
class FicheController extends AbstractController
{
    public function __construct(private PathUtils $pathUtils, private ClassementRepository $classementRepository, private FicheRepository $ficheRepository, private HoraireService $horaireService, private SearchEngineInterface $searchEngine, private HistoryUtils $historyUtils, private MessageBusInterface $messageBus)
    {
    }

    /**
     * Lists all Fiche entities.
     */
    #[Route(path: '/', name: 'bottin_admin_fiche_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $args = $fiches = [];
        if ($session->has('fiche_search')) {
            $args = json_decode($session->get('fiche_search'), true, 512, JSON_THROW_ON_ERROR);
        }
        $form = $this->createForm(SearchFicheType::class, $args, ['method' => 'GET']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $session->set('fiche_search', json_encode($args, JSON_THROW_ON_ERROR));

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

    /**
     * Displays a form to create a new Fiche fiche.
     *
     * @throws Exception
     */
    #[Route(path: '/new', name: 'bottin_admin_fiche_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $fiche = new Fiche();
        $fiche->setCp($this->getParameter('bottin.cp_default'));
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->ficheRepository->insert($fiche);

            $this->historyUtils->newFiche($fiche);
            $this->messageBus->dispatch(new FicheCreated($fiche->getId()));

            $this->addFlash('success', 'La fiche a bien été crée');

            return $this->redirectToRoute('bottin_admin_classement_new', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/admin/fiche/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Fiche fiche.
     */
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

    /**
     * Displays a form to edit an existing Fiche fiche.
     */
    #[Route(path: '/{id}/edit', name: 'bottin_admin_fiche_edit', methods: ['GET', 'POST'])]
    public function edit(Fiche $fiche, Request $request): Response
    {
        if ($fiche->getFtlb()) {
            $this->addFlash('warning', 'Vous ne pouvez pas éditer cette fiche car elle provient de la ftlb');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }
        $oldAdresse = $fiche->getRue().' '.$fiche->getNumero().' '.$fiche->getLocalite();
        $this->horaireService->initHoraires($fiche);
        $editForm = $this->createForm(FicheType::class, $fiche);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $editForm->getData();
            $horaires = $data->getHoraires();
            $this->horaireService->handleEdit($fiche, $horaires);

            try {
                $this->historyUtils->diffFiche($fiche);
            } catch (Exception) {
                $this->addFlash('danger', 'Erreur pour l\'enregistrement dans l\' historique');
            }

            $this->ficheRepository->flush();
            $this->addFlash('success', 'La fiche a bien été modifiée');

            $this->messageBus->dispatch(new FicheUpdated($fiche->getId(), $oldAdresse));

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
            $this->messageBus->dispatch(new FicheDeleted($fiche->getId()));
            $this->ficheRepository->remove($fiche);
            $this->ficheRepository->flush();

            $this->addFlash('success', 'La fiche a bien été supprimée');
        }

        return $this->redirectToRoute('bottin_admin_fiche_index');
    }
}
