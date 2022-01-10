<?php

namespace AcMarche\Bottin\Hades;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Hades\Entity\OffreInterface;

class HadesImport
{
    public function __construct(private HadesFactory $hadesFactory)
    {
    }

    public function treatment(OffreInterface $offre, Category $category): void
    {
        $fiche = $this->hadesFactory->createFiche($offre);
        $this->hadesFactory->setClassement($fiche, $category);
        $this->hadesFactory->setDescriptions($fiche, $offre->getDescriptions());
        $this->hadesFactory->setCoordonnees($fiche, $offre);
        $this->hadesFactory->flush();
    }
}
