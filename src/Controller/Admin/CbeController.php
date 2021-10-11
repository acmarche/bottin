<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Bce\Cache\CbeCache;
use AcMarche\Bottin\Bce\Repository\CbeRepository;
use AcMarche\Bottin\Entity\Fiche;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/admin/cbe")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class CbeController extends AbstractController
{
    private CbeRepository $cbeRepository;
    private CbeCache $cbeCache;

    public function __construct(CbeRepository $cbeRepository, CbeCache $cbeCache)
    {
        $this->cbeRepository = $cbeRepository;
        $this->cbeCache = $cbeCache;
    }

    /**
     * @Route("/{id}", name="bottin_admin_fiche_cbe", methods={"GET"})
     */
    public function show(Fiche $fiche): Response
    {
        $number = $fiche->getNumeroTva();
        $number = '0404.345.092';

        if (!$number) {
            $this->addFlash('warning', 'Veuillez remplir le numéro de TVA');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        $entreprise = $this->cbeCache->getCacheData($number);

        if (!$entreprise) {
            try {
                $entreprise = $this->cbeRepository->findByNumber($number);
            } catch (TransportExceptionInterface | \Exception $e) {
                $this->addFlash('warning', 'Erreur survenue: '.$e->getMessage());

                return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/cbe/show.html.twig',
            [
                'fiche' => $fiche,
                'entreprise' => $entreprise,
                'number' => $number,
            ]
        );
    }
}
