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
    private CategoryService $categoryService;
    private FicheRepository $ficheRepository;
    private ClassementRepository $classementRepository;

    public function __construct(
        CategoryService $categoryService,
        FicheRepository $ficheRepository,
        ClassementRepository $classementRepository
    ) {
        $this->categoryService = $categoryService;
        $this->ficheRepository = $ficheRepository;
        $this->classementRepository = $classementRepository;
    }

    /**
     * @Route("/admin/empty", name="bottin_admin_categories_empty")
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
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
     * @Route("/admin/secteur/principal", name="bottin_admin_secteur_principal")
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
    public function principal(): Response
    {
        $fiches = $this->ficheRepository->findAllWithJoins();
        $data = [];

        foreach ($fiches as $fiche) {
            $classements = $fiche->getClassements();
            if (1 == $classements->count()) {
                $classements[0]->setPrincipal(true);
            }
            $principaux = array_filter($classements->toArray(), function ($classement) {
                if ($classement->getPrincipal()) {
                    return true;
                }

                return false;
            });
            if (0 == count($principaux)) {
                $data[] = $fiche;
            }
        }

        $this->classementRepository->flush();

        return $this->render(
            '@AcMarcheBottin/admin/checkup/principal.html.twig',
            [
                'fiches' => $data,
            ]
        );
    }
}
