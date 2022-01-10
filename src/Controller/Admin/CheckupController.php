<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckupController extends AbstractController
{
    public function __construct(private CategoryService $categoryService, private FicheRepository $ficheRepository, private ClassementRepository $classementRepository)
    {
    }

    /**
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
    #[Route(path: '/admin/empty', name: 'bottin_admin_categories_empty')]
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

    /**
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
    #[Route(path: '/admin/secteur/principal', name: 'bottin_admin_secteur_principal')]
    public function principal(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $data = [];
        foreach ($fiches as $fiche) {
            $classements = $fiche->getClassements();
            $principaux = array_filter($classements->toArray(), fn ($classement) => (bool) $classement->getPrincipal());
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
