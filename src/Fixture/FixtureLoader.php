<?php

namespace AcMarche\Bottin\Fixture;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Repository\CategoryRepository;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FixtureLoader
{
    private \Fidry\AliceDataFixtures\LoaderInterface $loader;
    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;
    private \AcMarche\Bottin\Repository\CategoryRepository $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        LoaderInterface $loader,
        ParameterBagInterface $parameterBag
    ) {
        $this->loader = $loader;
        $this->parameterBag = $parameterBag;
        $this->categoryRepository = $categoryRepository;
    }

    public function getPath(): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/src/AcMarche/Bottin/src/Fixture/Files/';
    }

    public function load(): void
    {
        $path = $this->getPath();

        $files = [
            $path.'fiche.yaml',
            $path.'adresse.yaml',
            //  $path.'category.yaml',
            $path.'user.yaml',
        ];

        $this->loader->load($files, [], [], PurgeMode::createTruncateMode());
        $this->insert();
    }

    private function insert(): void
    {
        $category = $this->addRoot('Economie');
        $commerce = $this->addChild($category, 'Commerces');
        $commerce->setParent($category);

        $alimentation = $this->addChild($commerce, 'Alimentation');
        $alimentation->setParent($commerce);
        $this->addChild($alimentation, 'Boulanger');
        $this->addChild($alimentation, 'Fromager');

        $animaux = $this->addChild($commerce, 'Animaux');
        $animaux->setParent($commerce);
        $this->addChild($animaux, 'Colliers');
        $this->addChild($animaux, 'Croquettes');

        $sante = $this->addRoot('Sante');
        $medecins = $this->addChild($sante, 'Medecins');
        $medecins->setParent($sante);
        $this->addChild($sante, 'Pharmacie');
        $this->addChild($medecins, 'Gyneco');
        $this->addChild($medecins, 'General');
    }

    private function addRoot(string $name): Category
    {
        $category = new Category();
        $category->setName($name);
        $this->categoryRepository->persist($category);
        $this->categoryRepository->flush();

        return $category;
    }

    private function addChild(Category $category, string $name): Category
    {
        $child = new Category();
        $child->setName($name);
        $this->categoryRepository->persist($child);
        $this->categoryRepository->flush();
        $child->setParent($category);
        $child->setChildNodeOf($category);
        $this->categoryRepository->flush();

        return $child;
    }
}
