<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\Search\SearchHistoryType;
use AcMarche\Bottin\Repository\HistoryRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/admin/history')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class HistoryController extends AbstractController
{
    public function __construct(private readonly HistoryRepository $historyRepository)
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
