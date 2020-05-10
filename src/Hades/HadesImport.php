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

    public function treatment(OffreInterface $offre,Category $category) {
        $fiche = $this->hadesFactory->createFiche($offre);
        $this->hadesFactory->setClassement($fiche, $category);
        $this->hadesFactory->setDescriptions($fiche, $offre->getDescription());
        $fiche->setComment1($offre->getTitre());
    }

    public function traitementOffre($offre, Category $category)
    {
        $localisation = $this->hades->getLocalisations($offre);
        $descriptions = $this->hades->getDescriptions($offre);
        $contacts = $this->hades->getContacts($offre);

        $this->output->writeln($titre);

        if (count($contacts) > 0) {
            $this->setContacts($fiche, $contacts);
        }

        if (count($descriptions) > 0) {
            $this->setDescriptions($fiche, $descriptions);
        }

        $fiche->setLatitude($localisation['latitude']);
        $fiche->setLongitude($localisation['longitude']);
        $fiche->setLocalite($localisation['localite_nom']);
        $fiche->setCp($localisation['code_postal']);

        $this->classementRepository->flush();
        $this->ficheRepository->flush();
    }
}
