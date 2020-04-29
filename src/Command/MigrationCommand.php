<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\HoraireRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrationCommand extends Command
{
    protected static $defaultName = 'bottin:migration';
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var HoraireRepository
     */
    private $horaireRepository;

    public function __construct(
        FicheRepository $ficheRepository,
        CategoryRepository $categoryRepository,
        HoraireRepository $horaireRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->ficheRepository = $ficheRepository;
        $this->categoryRepository = $categoryRepository;
        $this->horaireRepository = $horaireRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /*  foreach ($this->ficheRepository->findAll() as $fiche) {
              $fiche->setCreatedAt(new \DateTime());
              $fiche->setUpdatedAt(new \DateTime());
          }
          $this->ficheRepository->flush();*/

        $this->setChild();

        foreach ($this->horaireRepository->findAll() as $horaire) {
            $horaire->setCreatedAt(new \DateTime());
            $horaire->setUpdatedAt(new \DateTime());
        }
        $this->horaireRepository->flush();

        return 0;
    }

    private function setChild()
    {
        foreach ($this->categoryRepository->getRootsOld() as $root) {
            $root->setCreatedAt(new \DateTime());
            $root->setUpdatedAt(new \DateTime());
            foreach ($this->categoryRepository->getChildrenOld($root) as $category) {
                $category->setChildNodeOf($root);
                $this->categoryRepository->flush();
                foreach ($this->categoryRepository->getChildrenOld($category) as $child) {
                    $child->setChildNodeOf($category);
                    $this->categoryRepository->flush();
                    foreach ($this->categoryRepository->getChildrenOld($child) as $child2) {
                        $child2->setChildNodeOf($child);
                        $this->categoryRepository->flush();
                    }
                }
            }
        }
        $this->categoryRepository->flush();
    }


}
