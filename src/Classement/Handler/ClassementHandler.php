<?php

namespace AcMarche\Bottin\Classement\Handler;

use AcMarche\Bottin\Bottin;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

class ClassementHandler
{
    public function __construct(private readonly ClassementRepository $classementRepository, private readonly CategoryRepository $categoryRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function handleNewClassement(Fiche $fiche, ?int $categoryId): Classement
    {
        if (!$categoryId) {
            throw new Exception('La référence à la rubrique n\'a pas été trouvée');
        }

        $category = $this->categoryRepository->find($categoryId);

        if (!$category instanceof Category) {
            throw new Exception('La catégorie n\'a pas été trouvée.');
        }

        if ($this->classementRepository->checkExist($fiche, $category) instanceof Classement) {
            throw new Exception('La fiche est déjà classée dans cette rubrique');
        }

        $classement = new Classement($fiche, $category);
        $category = $this->categoryRepository->getTree($category->getRealMaterializedPath());

        if ($category->getChildNodes()->count() > 0) {
            throw new Exception('Vous ne pouvez pas classer dans une rubrique qui contient des sous éléments');
        }

        $this->classementRepository->insert($classement);

        return $classement;
    }

    /**
     * @return Classement[]
     */
    public function getClassements(Fiche $fiche): array
    {
        return $this->classementRepository->getByFiche($fiche);
    }

    public function getRoot(Classement $classement): int
    {
        foreach (Bottin::ROOTS as $root) {
            $eco = $this->categoryRepository->find($root);
            if ($classement->getCategory()->isIndirectChildNodeOf($eco)) {
                return $root;
            }
        }

        throw new Exception('Root not found '.$classement);
    }
}
