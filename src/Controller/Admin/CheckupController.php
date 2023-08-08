<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckupController extends AbstractController
{
    public function __construct(private readonly CategoryService $categoryService, private readonly FicheRepository $ficheRepository, private readonly ClassementRepository $classementRepository)
    {
    }

    #[Route(path: '/admin/empty', name: 'bottin_admin_categories_empty')]
    #[IsGranted('ROLE_BOTTIN_ADMIN')]
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

    #[Route(path: '/admin/secteur/principal', name: 'bottin_admin_secteur_principal')]
    #[IsGranted('ROLE_BOTTIN_ADMIN')]
    public function principal(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $data = [];
        foreach ($fiches as $fiche) {
            $classements = $fiche->getClassements();
            $principaux = array_filter($classements->toArray(), static fn ($classement) => (bool) $classement->getPrincipal());
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
