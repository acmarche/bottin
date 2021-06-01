<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct(FicheRepository $ficheRepository
    ) {
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Route("/", name="bottin_home")
     */
    public function index()
    {
        return $this->render(
            '@AcMarcheBottin/default/index.html.twig'
        );
    }

    /**
     * @Route("/uuid", name="bottin_uuid")
     */
    public function uuid()
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        foreach ($fiches as $fiche) {
            $fiche->setUuid($fiche->generateUuid());
        }

        return $this->render(
            '@AcMarcheBottin/default/uuid.html.twig',
            ['fiches' => $fiches]
        );
    }
}
