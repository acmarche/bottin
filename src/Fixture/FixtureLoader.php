<?php

namespace AcMarche\Bottin\Fixture;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Repository\CategoryRepository;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FixtureLoader
{
    /**
     * @var LoaderInterface
     */
    private $loader;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

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

    private function insert()
    {
        $economie = $this->addRoot('Economie');
        $commerce = $this->addChild($economie, 'Commerces');
        $commerce->setParent($economie);

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
        $child->setParent($parent);
        $child->setChildNodeOf($parent);
        $this->categoryRepository->flush();

        return $child;
    }
}
