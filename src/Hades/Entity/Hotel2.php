<?php


namespace AcMarche\Bottin\Hades\Entity;

class Hotel2
{
    /**
     * @var Titre[]
     */
    public array $titre;
    public $modif_date;
    public $publiable;
    public $oldkey;
    public $oldtab;
    public $off_id_ref;
    /**
     * @var Categories[]
     */
    public array $categories;
    /**
     * @var Localisation[]
     */
    public array $localisation;
    /**
     * @var Geocodes[]
     */
    public array $geocodes;
    /**
     * @var Descriptions[]
     */
    public array $descriptions;
    public $contacts;
    public $attributs;
    public $tarifs;
    public $medias;
    public $parents;
    public $enfants;
    public $selections;

    public function getTitre(string $lang = 'fr'): string
    {
        $lookup = array_column($this->titre, 'value', 'lg');

        if (isset($lookup[$lang])) {
            return $lookup[$lang];
        }

        return 'Nom non trouvé';
    }
}
