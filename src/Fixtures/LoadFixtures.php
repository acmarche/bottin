<?php


namespace AcMarche\Bottin\Fixtures;


use AcMarche\Bottin\Entity\Category;

class LoadFixtures
{
    private function insert()
    {
        $ecnomoie = $this->addRoot('Economie');
        $commerce = $this->addChild($ecnomoie, 'Commerces');
        $alimentation = $this->addChild($commerce, 'Alimentation');
        $animaux = $this->addChild($commerce, 'Animaux');
        $this->addChild($alimentation, 'Boulanger');
        $this->addChild($alimentation, 'Fromager');
        $this->addChild($animaux, 'Colliers');
        $this->addChild($animaux, 'Croquettes');

        $sante = $this->addRoot('Sante');
        $medecins = $this->addChild($sante, 'Medecins');
        $this->addChild($sante, 'Pharmacie');
        $this->addChild($medecins, 'Gyneco');
        $this->addChild($medecins, 'General');
    }

    private function addRoot(string $name): Category
    {
        $root = new Category();
        $root->setName($name);
        $this->categoryRepository->persist($root);
        $this->categoryRepository->flush();

        return $root;
    }

    private function addChild(Category $parent, string $name): Category
    {
        $child = new Category();
        $child->setName($name);
        $this->categoryRepository->persist($child);
        $this->categoryRepository->flush();
        $child->setChildNodeOf($parent);
        $this->categoryRepository->flush();

        return $child;
    }

}
