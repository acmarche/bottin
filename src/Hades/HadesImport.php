<?php


namespace AcMarche\Bottin\Hades;


use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Hades\Entity\OffreInterface;

class HadesImport
{
    /**
     * @var HadesFactory
     */
    private $hadesFactory;
    /**
     * @var HadesParser
     */
    private $hadesParser;

    public function __construct(HadesFactory $hadesFactory, HadesParser $hadesParser)
    {
        $this->hadesFactory = $hadesFactory;
        $this->hadesParser = $hadesParser;
    }

    public function treatment(OffreInterface $offre, Category $category)
    {
        $fiche = $this->hadesFactory->createFiche($offre);
        $this->hadesFactory->setClassement($fiche, $category);
        $this->hadesFactory->setDescriptions($fiche, $offre->getDescriptions());
        $this->hadesFactory->setCoordonnees($fiche, $offre);
        $this->hadesFactory->flush();
    }
}
