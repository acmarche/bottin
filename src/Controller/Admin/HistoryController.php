<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\Search\SearchHistoryType;
use AcMarche\Bottin\Repository\HistoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 */
#[Route(path: '/admin/history')]
#[IsGranted(data: 'ROLE_BOTTIN_ADMIN')]
class HistoryController extends AbstractController
{
    public function __construct(private HistoryRepository $historyRepository)
    {
    }

    #[Route(path: '/fiche/{id}', name: 'bottin_admin_history_fiche')]
    public function fiche(Fiche $fiche): Response
    {
        $histories = $this->historyRepository->findByFiche($fiche);

        return $this->render(
            '@AcMarcheBottin/admin/history/fiche.html.twig',
            [
                'fiche' => $fiche,
                'histories' => $histories,
            ]
        );
    }

    #[Route(path: '/', name: 'bottin_admin_history_index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchHistoryType::class, [], ['method' => 'GET']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $args = $form->getData();
            $histories = $this->historyRepository->search($args['nom'], $args['madeBy'], $args['property']);
        } else {
            $histories = $this->historyRepository->findOrdered();
        }

        return $this->render(
            '@AcMarcheBottin/admin/history/index.html.twig',
            [
                'histories' => $histories,
                'form' => $form->createView(),
                'search' => $form->isSubmitted(),
            ]
        );
    }
}
