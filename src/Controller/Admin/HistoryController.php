<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\HistoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/admin/history")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class HistoryController extends AbstractController
{
    private HistoryRepository $historyRepository;

    public function __construct(HistoryRepository $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    /**
     * @Route("/fiche/{id}", name="bottin_admin_history_fiche")
     */
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

    /**
     * @Route("/", name="bottin_admin_history_index")
     */
    public function index(): Response
    {
        $histories = $this->historyRepository->findOrdered();
dump($histories);
        return $this->render(
            '@AcMarcheBottin/admin/history/index.html.twig',
            [
                'histories' => $histories,
            ]
        );
    }

}
