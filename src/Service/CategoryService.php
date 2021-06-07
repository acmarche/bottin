<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 11/01/19
 * Time: 11:38.
 */

namespace AcMarche\Bottin\Service;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Utils\PathUtils;
use AcMarche\Bottin\Utils\SortUtils;
use Doctrine\Common\Collections\ArrayCollection;

class CategoryService
{
    private \AcMarche\Bottin\Repository\CategoryRepository $categoryRepository;
    private array $data = [];
    private \AcMarche\Bottin\Repository\ClassementRepository $classementRepository;
    private \AcMarche\Bottin\Utils\PathUtils $pathUtils;

    public function __construct(
        CategoryRepository $categoryRepository,
        ClassementRepository $classementRepository,
        PathUtils $pathUtils
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * @return Category[]
     */
    public function getEmpyCategories(): iterable
    {
        $roots = $this->categoryRepository->getRootNodes();

        foreach ($roots as $root) {
            $level2 = $this->categoryRepository->getTree($root->getRealMaterializedPath())->getChildNodes();
            foreach ($level2 as $enfant) {
                $level3 = $this->categoryRepository->getTree($enfant->getRealMaterializedPath())->getChildNodes();
                foreach ($level3 as $petitEnfant) {
                    $this->hasFiches($petitEnfant);
                    $level4 = $this->categoryRepository->getTree(
                        $petitEnfant->getRealMaterializedPath()
                    )->getChildNodes();
                    foreach ($level4 as $petitPetitEnfant) {
                        $this->hasFiches($petitPetitEnfant);
                    }
                }
            }
        }

        return $this->data;
    }

    private function hasFiches(Category $category): void
    {
        $classements = $this->classementRepository->findBy(['category' => $category]);
        if (0 == count($classements)) {
            $category->setPath($this->pathUtils->getPath($category));
            $this->data[] = $category;
        }
    }

    /**
     * @param Category $category
     *
     * @return Fiche[]
     */
    public function getFichesByCategoryAndHerChildren(Category $category): array
    {
        $categories = $this->categoryRepository->getFlatTree($category->getRealMaterializedPath());
        $classements = $this->classementRepository->findBy(['category' => $categories]);

        $fiches = array_column($classements, 'fiche', 'id');
        $arrayCollection = new ArrayCollection();

        foreach ($fiches as $fiche) {
            if (!$arrayCollection->contains($fiche)) {
                $arrayCollection->add($fiche);
            }
        }

        return SortUtils::sortFiche($arrayCollection->toArray());
    }

    /**
     * @param int $idCategory
     * @return Fiche[]
     */
    public function getFichesByCategoryId(int $idCategory): array
    {
        return $this->getFichesByCategoryAndHerChildren($this->categoryRepository->find($idCategory));
    }

    protected function test(Category $category): void
    {
        //Returns a node hydrated with its children and parents
        ($this->categoryRepository->getTree($category->getRealMaterializedPath()));
        //tout l'arbre sauf une branche et ses enfants, flat
        ($this->categoryRepository->getTreeExceptNodeAndItsChildrenQB($category)->getQuery()->getResult());
        //all childs flat
        ($this->categoryRepository->getFlatTree($category->getRealMaterializedPath()));
    }
}
