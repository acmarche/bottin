<?php

namespace AcMarche\Bottin\Repository;

use AcMarche\Bottin\Cap\Cap;
use AcMarche\Bottin\Entity\Category;
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
    use TreeTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
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

    public function getRubriquesShopping(): array
    {
        $rubriques = [];

        $commerces = $this->getDirectChilds(Cap::idCommerces); //commerces-entreprises
        foreach ($commerces as $rubrique) {
            $id = $rubrique->getId();
            $enfants = [];
            $enfantsTmp = $this->getDirectChilds($id);

            foreach ($enfantsTmp as $enfant) {
                $enfants[] = $enfant;
            }
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
        $liberales = $this->find(Cap::idLiberales);
        $liberales->setEnfants($this->getDirectChilds(Cap::idLiberales));
        $rubriques[] = $liberales;

        return $rubriques;
    }

    /**
     * Manipulates the flat tree query builder before executing it.
     * Override this method to customize the tree query.
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
