<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\DoctrineBehaviors\ORM\Tree\TreeTrait;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    use TreeTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param string|null $name
     * @param Category|null $parent
     * @return Category[]
     */
    public function search(?string $name = null, ?Category $parent = null)
    {
        $qb = $this->createQueryBuilder('category');

        if ($name) {
            $qb->andWhere('category.name LIKE :nom')
                ->setParameter('nom', '%'.$name.'%');
        }

        if ($parent) {
            $qb->andWhere('category.parent = :root')
                ->setParameter('root', $parent);
        }

        $qb->orderBy('category.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getAllTree()
    {
        $categories = [];
        foreach ($this->getRootNodes() as $rootNode) {
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
     * @param int $parentId
     * @return Category[]
     */
    public function getDirectChilds(int $parentId)
    {
        return $this->createQueryBuilder('category')
            ->andWhere('category.parent = :categorie')
            ->setParameter('categorie', $parentId)
            ->orderBy('category.name', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getRubriquesShopping(): array
    {
        $rubriques = array();

        $commerces = $this->getDirectChilds(Cap::idCommerces); //commerces-entreprises
        foreach ($commerces as $rubrique) {
            $id = $rubrique->getId();
            $enfants = array();
            $enfantsTmp = $this->getDirectChilds($id);

            foreach ($enfantsTmp as $enfant) {
                $enfants[] = $enfant;
            }
            /**
             * ajout de pharmacie dans branche eco => sante
             */
            if ($id == Cap::idSanteEco) {
                $enfants[] = $this->find(Cap::idPharmacies);
            }

            $rubrique->setEnfants($enfants);

            $rubriques[] = $rubrique;
        }

        /**
         * ajout des professions liberales
         */
        $liberales = $this->find(Cap::idLiberales);
        $liberales->setEnfants($this->getDirectChilds(Cap::idLiberales));
        $rubriques[] = $liberales;

        return $rubriques;
    }

    /**
     * @param Category $category
     * @return Category[]
     */
    public function getChildrenOld(Category $category)
    {
        $qb = $this->createQueryBuilder('category');
        $qb->andWhere('category.parent = :categorie')
            ->setParameter('categorie', $category);

        $qb->orderBy('category.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function getRootsOld()
    {
        $qb = $this->createQueryBuilder('category');

        $qb->andWhere('category.parent IS NULL');

        $qb->orderBy('category.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Manipulates the flat tree query builder before executing it.
     * Override this method to customize the tree query
     */
    protected function addFlatTreeConditions(QueryBuilder $queryBuilder, array $extraParams): void
    {
    }

    public function persist(Category $category)
    {
        $this->_em->persist($category);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function remove(Category $category)
    {
        $this->_em->remove($category);
    }

}
