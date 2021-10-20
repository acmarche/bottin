<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bce\Cache\CbeCache;
use AcMarche\Bce\Repository\CbeRepository;
use AcMarche\Bottin\Entity\Fiche;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/admin/bce")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class BceController extends AbstractController
{
    private CbeRepository $bceRepository;
    private CbeCache $bceCache;

    public function __construct(CbeRepository $bceRepository, CbeCache $bceCache)
    {
        $this->bceRepository = $bceRepository;
        $this->bceCache = $bceCache;
    }

    /**
     * @Route("/{id}", name="bottin_admin_fiche_bce", methods={"GET"})
     */
    public function show(Fiche $fiche): Response
    {
        $number = $fiche->getNumeroTva();

        if (!$number) {
            $this->addFlash('warning', 'Veuillez remplir le numéro de TVA');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }

        $entreprise = $this->bceCache->getCacheData($number);

        if (!$entreprise) {
            try {
                $entreprise = $this->bceRepository->findByNumber($number);
            } catch (TransportExceptionInterface | \Exception $e) {
                $this->addFlash('warning', 'Erreur survenue: '.$e->getMessage());

                return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/bce/show.html.twig',
            [
                'fiche' => $fiche,
                'entreprise' => $entreprise,
                'number' => $number,
            ]
        );
    }
}
