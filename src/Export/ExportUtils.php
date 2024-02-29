<?php

namespace AcMarche\Bottin\Export;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\SelectionRepository;
use AcMarche\Bottin\Utils\SortUtils;

class ExportUtils
{
    public function __construct(
        private readonly SelectionRepository $selectionRepository,
        private readonly CategoryService $categoryService,
        private readonly FicheRepository $ficheRepository
    ) {
    }

    /**
     * @return array|Fiche[]
     */
    public function getFichesBySelection(string $username): array
    {
        $selections = $this->selectionRepository->findByUser($username);
        $categories = [];
        foreach ($selections as $selection) {
            $categories[] = $selection->category;
        }

        if ([] !== $categories) {
            $fiches = $this->categoryService->getFichesByCategoriesAndHerChildren($categories);
        } else {
            $fiches = $this->ficheRepository->findAllWithJoins();
        }

        return SortUtils::sortFiche($fiches);
    }
}
