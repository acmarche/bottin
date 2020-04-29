<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Elastic\AggregationUtils;
use AcMarche\Bottin\Elastic\ElasticServer;
use AcMarche\Bottin\Elastic\SuggestUtils;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\FicheType;
use AcMarche\Bottin\Form\Search\SearchFicheType;
use AcMarche\Bottin\Message\FicheCreated;
use AcMarche\Bottin\Message\FicheDeleted;
use AcMarche\Bottin\Message\FicheUpdated;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\GeolocalisationService;
use AcMarche\Bottin\Service\HoraireService;
use AcMarche\Bottin\Utils\PathUtils;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Fiche controller.
 *
 * @Route("/fiche")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class FicheController extends AbstractController
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var HoraireService
     */
    private $horaireService;
    /**
     * @var GeolocalisationService
     */
    private $geolocalisationService;
    /**
     * @var ElasticServer
     */
    private $elasticServer;
    /**
     * @var AggregationUtils
     */
    private $aggregationUtils;
    /**
     * @var SuggestUtils
     */
    private $suggestUtils;
    /**
     * @var ClassementRepository
     */
    private $classementRepository;
    /**
     * @var PathUtils
     */
    private $pathUtils;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        GeolocalisationService $geolocalisationService,
        PathUtils $pathUtils,
        ClassementRepository $classementRepository,
        FicheRepository $ficheRepository,
        HoraireService $horaireService,
        ElasticServer $elasticServer,
        AggregationUtils $aggregationUtils,
        SuggestUtils $suggestUtils,
        SerializerInterface $serializer
    ) {
        $this->ficheRepository = $ficheRepository;
        $this->horaireService = $horaireService;
        $this->geolocalisationService = $geolocalisationService;
        $this->elasticServer = $elasticServer;
        $this->aggregationUtils = $aggregationUtils;
        $this->suggestUtils = $suggestUtils;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
        $this->serializer = $serializer;
    }

    /**
     * Lists all Fiche entities.
     *
     * @Route("/", name="bottin_fiche", methods={"GET"})
     * @Route("/search/{keyword}", name="bottin_fiche_search", methods={"GET"})
     *
     */
    public function index(Request $request, ?string $keyword)
    {
        $session = $request->getSession();
        $args = $hits = $suggest = $response = [];

        if ($session->has('fiche_search')) {
            $args = json_decode($session->get('fiche_search'), true);
        }

        if ($keyword) {
            $args['nom'] = $keyword;
        }
        $search_form = $this->createForm(SearchFicheType::class, $args, ['method' => 'GET',]);

        $search_form->handleRequest($request);

        if ($search_form->isSubmitted() && $search_form->isValid()) {
            $args = $search_form->getData();
            $session->set('fiche_search', json_encode($args));

            if ($search_form->get('raz')->isClicked()) {
                $session->remove('fiche_search');
                $this->addFlash('info', 'La recherche a bien été réinitialisée.');

                return $this->redirectToRoute('bottin_fiche');
            }
            try {
                $response = $this->elasticServer->doSearch($args['nom'], $args['localite'], $args['type']);
                $response = $this->elasticServer->doSearchForCap($args['nom']);
                dump($response);
                $hits = $response['hits'];
            } catch (BadRequest400Exception $e) {
                $this->addFlash('danger', 'Erreur dans la recherche: '.$e->getMessage());
            }
        }

        return $this->render(
            '@AcMarcheBottin/fiche/index.html.twig',
            [
                'search_form' => $search_form->createView(),
                'hits' => $hits,
                'localites' => $this->aggregationUtils->getLocalites($response),
                'pmr' => $this->aggregationUtils->countPmr($response),
                'midi' => $this->aggregationUtils->countMidi($response),
                'centre_ville' => $this->aggregationUtils->countCentreVille($response),
                'suggests' => $this->suggestUtils->getOptions($response),
            ]
        );
    }

    /**
     * Displays a form to create a new Fiche fiche.
     *
     * @Route("/new", name="bottin_fiche_new", methods={"GET", "POST"})
     *
     * @throws \Exception
     */
    public function new(Request $request)
    {
        $fiche = new Fiche();
        $fiche->setCp($this->getParameter('bottin.cp_default'));

        $form = $this->createForm(FicheType::class, $fiche);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ficheRepository->insert($fiche);

            $this->dispatchMessage(new FicheCreated($fiche->getId()));

            $this->addFlash('success', 'La fiche a bien été crée');

            return $this->redirectToRoute('bottin_fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/fiche/new.html.twig',
            [
                'fiche' => $fiche,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Finds and displays a Fiche fiche.
     *
     * @Route("/{id}", name="bottin_fiche_show", methods={"GET"})
     */
    public function show(Fiche $fiche)
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        return $this->render(
            '@AcMarcheBottin/fiche/show.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );
    }

    /**
     * Displays a form to edit an existing Fiche fiche.
     *
     * @Route("/{id}/edit", name="bottin_fiche_edit", methods={"GET", "POST"})
     */
    public function edit(Fiche $fiche, Request $request)
    {
        $oldRue = $fiche->getRue();
        $this->horaireService->initHoraires($fiche);

        $editForm = $this->createForm(FicheType::class, $fiche);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->dispatchMessage(new FicheUpdated($fiche->getId(), $oldRue));

            $data = $editForm->getData();
            $horaires = $data->getHoraires();
            $this->horaireService->handleEdit($fiche, $horaires);

            $this->ficheRepository->flush();

            $this->addFlash('success', 'La fiche a bien été modifiée');

            return $this->redirectToRoute('bottin_fiche_show', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/fiche/edit.html.twig',
            [
                'fiche' => $fiche,
                'form' => $editForm->createView(),
            ]
        );
    }


    /**
     * @Route("/{id}", name="bottin_fiche_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Fiche $fiche): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fiche->getId(), $request->request->get('_token'))) {
            $this->ficheRepository->remove($fiche);
            $this->ficheRepository->flush();

            $this->dispatchMessage(new FicheDeleted($fiche->getId()));

            $this->addFlash('success', "La fiche a bien été supprimée");
        }

        return $this->redirectToRoute('bottin_fiche');
    }
}
