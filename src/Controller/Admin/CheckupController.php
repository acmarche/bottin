<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_BOTTIN_ADMIN')]
#[Route(path: '/admin/checkup')]
class CheckupController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly FicheRepository $ficheRepository,
    ) {
    }

    #[Route(path: '/', name: 'bottin_admin_checkup_index')]
    public function index(): Response
    {
        $categories = $this->categoryService->getEmpyCategories();

        return $this->render(
            '@AcMarcheBottin/admin/checkup/index.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }


    #[Route(path: '/empty', name: 'bottin_admin_checkup_categories_empty')]
    public function empty(): Response
    {
        $categories = $this->categoryService->getEmpyCategories();

        return $this->render(
            '@AcMarcheBottin/admin/checkup/empty.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }

    #[Route(path: '/empty/classement', name: 'bottin_admin_checkup_classement_empty')]
    public function withoutClassement(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $data = [];
        foreach ($fiches as $fiche) {
            if (0 == \count($fiche->classements)) {
                $data[] = $fiche;
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/checkup/noclassement.html.twig',
            [
                'fiches' => $data,
            ]
        );
    }

    #[Route(path: '/secteur/principal', name: 'bottin_admin_secteur_principal')]
    public function principal(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $data = [];
        foreach ($fiches as $fiche) {
            $classements = $fiche->classements;
            $principaux = array_filter(
                $classements->toArray(),
                static fn($classement) => $classement->principal
            );
            if (0 == \count($principaux)) {
                $data[] = $fiche;
            }
        }

        return $this->render(
            '@AcMarcheBottin/admin/checkup/principal.html.twig',
            [
                'fiches' => $data,
            ]
        );
    }
}
