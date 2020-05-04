<?php


namespace AcMarche\Bottin\Classement\Handler;


use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;

class ClassementHandler
{
    /**
     * @var ClassementRepository
     */
    private $classementRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(ClassementRepository $classementRepository, CategoryRepository $categoryRepository)
    {
        $this->classementRepository = $classementRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Fiche $fiche
     * @param int|null $categoryId
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function handleNewClassement(Fiche $fiche, ?int $categoryId)
    {
        if (!$categoryId) {
            throw new \Exception('La référence à la rubrique n\'a pas été trouvée');
        }

        $category = $this->categoryRepository->find($categoryId);

        if (!$category) {
            throw new \Exception('La catégorie n\'a pas été trouvée.');
        }

        if ($this->classementRepository->checkExist($fiche, $category)) {
            throw new \Exception('La fiche est déjà classée dans cette rubrique');
        }

        $classement = new Classement($fiche, $category);
        $category = $this->categoryRepository->getTree($category->getRealMaterializedPath());

        if ($category->getChildNodes()->count() > 0) {
            throw new \Exception('Vous ne pouvez pas classer dans une rubrique qui contient des enfants');
        }

        $this->classementRepository->insert($classement);
    }
}
