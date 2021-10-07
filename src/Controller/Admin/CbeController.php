<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Cbe\Repository\CbeRepository;
use AcMarche\Bottin\Entity\Fiche;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/cbe")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class CbeController extends AbstractController
{
    private CbeRepository $cbeRepository;

    public function __construct(CbeRepository $cbeRepository)
    {
        $this->cbeRepository = $cbeRepository;
    }

    /**
     * @Route("/{id}", name="bottin_admin_fiche_cbe", methods={"GET"})
     */
    public function show(Fiche $fiche): Response
    {
        $number = $fiche->getNumeroTva();
        $number = '0404.345.092';
        $entreprise = $this->cbeRepository->findByNumber($number);

        return $this->render(
            '@AcMarcheBottin/admin/cbe/show.html.twig',
            [
                'fiche' => $fiche,
                'entreprise' => $entreprise,
            ]
        );
    }

}
