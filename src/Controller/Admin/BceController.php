<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bce\Cache\CbeCache;
use AcMarche\Bce\Repository\CbeRepository;
use AcMarche\Bottin\Entity\Fiche;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route(path: '/admin/bce')]
#[IsGranted(data: 'ROLE_BOTTIN_ADMIN')]
class BceController extends AbstractController
{
    public function __construct(private CbeRepository $bceRepository, private CbeCache $bceCache)
    {
    }

    #[Route(path: '/{id}', name: 'bottin_admin_fiche_bce', methods: ['GET'])]
    public function show(Fiche $fiche): Response
    {
        $number = $fiche->getNumeroTva();
        if (!$number) {
            $this->addFlash('warning', 'Veuillez remplir le numéro de TVA');

            return $this->redirectToRoute('bottin_admin_fiche_show', ['id' => $fiche->getId()]);
        }
        $entreprise = $this->bceCache->getCacheData($number);
        if (null === $entreprise) {
            try {
                $entreprise = $this->bceRepository->findByNumber($number);
            } catch (TransportExceptionInterface | Exception $e) {
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
