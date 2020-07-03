<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Cap\ApiUtils;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ChequeController extends AbstractController
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var ApiUtils
     */
    private $apiUtils;

    public function __construct(FicheRepository $ficheRepository, ApiUtils $apiUtils)
    {
        $this->ficheRepository = $ficheRepository;
        $this->apiUtils = $apiUtils;
    }

    /**
     * @Route("/cheque", name="cheque")
     */
    public function index()
    {
        /**
         * @var array $json
         */
        $json = json_decode(file_get_contents($this->getParameter('kernel.project_dir').'/data/cheques.json'), true);

        $ids = array_column($json, 'Identifiant');
        $fiches = $this->ficheRepository->findByIds($ids);
        foreach ($fiches as $fiche) {
            $latitude = $fiche->getLatitude();
            $longitude = $fiche->getLongitude();
            $horaires = $this->apiUtils->getHorairesForApi($fiche);
            $images = $this->apiUtils->getImages($fiche);
            $key = array_search($fiche->getId(), $ids);
            $json[$key]['latitude'] = $latitude;
            $json[$key]['longitude'] = $longitude;
            $json[$key]['horaires'] = $horaires;
            $json[$key]['images'] = $images;
        }

        return JsonResponse::fromJsonString(json_encode($json));
    }
}
