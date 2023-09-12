<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Doctrine\OrmCrudTrait;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Utils\SortUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\DoctrineBehaviors\ORM\Tree\TreeTrait;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;
    use TreeTrait;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function search(string $name = null, Category $category = null): array
    {
        $queryBuilder = $this->createQueryBuilder('category');

        if ($name) {
            $queryBuilder->andWhere('category.name LIKE :nom')
                ->setParameter('nom', '%'.$name.'%');
        }

        if ($category instanceof Category) {
            $queryBuilder->andWhere('category.parent = :root')
                ->setParameter('root', $category);
        }

        $queryBuilder->orderBy('category.name', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getAllTree(): array
    {
        $categories = [];
        $roots = $this->getRootNodes();
        $roots = SortUtils::sortCategories($roots);
        foreach ($roots as $rootNode) {
            $categories[] = $this->getTree($rootNode->getRealMaterializedPath());
        }

        $data = [];
        foreach ($categories as $root) {
            $data[$root->getId()] = $root;
            foreach ($root->getChildNodes() as $level2) {
                $data[$level2->getId()] = $level2;
                foreach ($level2->getChildNodes() as $level3) {
                    $data[$level3->getId()] = $level3;
                }
            }
        }

        return $data;
    }

    /**
     * @return Category[]
     */
    public function getDirectChilds(int $parentId): array
    {
        return $this->createQueryBuilder('category')
            ->andWhere('category.parent = :categorie')
            ->setParameter('categorie', $parentId)
            ->orderBy('category.name', 'ASC')
            ->getQuery()->getResult();
    }

    public function getRubriquesShopping(): array
    {
        $rubriques = [];

        $commerces = $this->getDirectChilds(Cap::idCommerces); // commerces-entreprises
        foreach ($commerces as $rubrique) {
            // skip industrie
            if (690 === $rubrique->getId()) {
                continue;
            }

            $id = $rubrique->getId();
            $enfants = [];
            $enfantsTmp = $this->getDirectChilds($id);

            $enfants = $enfantsTmp;
            /*
             * ajout de pharmacie dans branche eco => sante
             */
            if (Cap::idSanteEco == $id) {
                $enfants[] = $this->find(Cap::idPharmacies);
            }

            $rubrique->setEnfants($enfants);

            $rubriques[] = $rubrique;
        }

        /**
         * ajout des professions liberales.
         */
        $category = $this->find(Cap::idLiberales);
        $category->setEnfants($this->getDirectChilds(Cap::idLiberales));
        $rubriques[] = $category;

        return $rubriques;
    }

    public function findOneBySlug(string $slug): ?Category
    {
        return $this->createQueryBuilder('category')
            ->andWhere('category.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('category.name', 'ASC')
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * Manipulates the flat tree query builder before executing it. Override this method to customize the tree query.
     */
    protected function addFlatTreeConditions(QueryBuilder $queryBuilder, array $extraParams): void
    {
        $queryBuilder->addOrderBy('t.name');
    }
}
