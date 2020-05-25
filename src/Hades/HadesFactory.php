<?php


namespace AcMarche\Bottin\Hades;


use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Hades\Entity\OffreInterface;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;

class HadesFactory
{
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var ClassementRepository
     */
    private $classementRepository;

    public function __construct(FicheRepository $ficheRepository, ClassementRepository $classementRepository)
    {
        $this->ficheRepository = $ficheRepository;
        $this->classementRepository = $classementRepository;
    }

    public function createFiche(OffreInterface $offre): Fiche
    {
        $fiche = $this->ficheRepository->findOneBy(['ftlb' => $offre->getId()]);

        if (!$fiche) {
            $fiche = new Fiche();
            $fiche->setUser('ftlb');
            $fiche->setFtlb($offre->getId());
            $fiche->setSociete($offre->getTitre());//bug slug
            $this->ficheRepository->persist($fiche);
        }

        $fiche->setSociete($offre->getTitre());

        return $fiche;
    }

    public function setClassement(Fiche $fiche, Category $category)
    {
        if (count($fiche->getClassements()) > 0) {
            return;
        }

        $classement = new Classement($fiche, $category);
        $classement->setPrincipal(true);
        $this->classementRepository->persist($classement);
    }

    public function setDescriptions(Fiche $fiche, array $descriptions)
    {
        if (count($descriptions) == 0) {
            return;
        }

        $fiche->setComment1($descriptions[0]);

        if (isset($descriptions[1])) {
            $fiche->setComment2($descriptions[1]);
        }

        if (isset($descriptions[2])) {
            $fiche->setComment3($descriptions[2]);
        }
    }

    public function setCoordonnees(Fiche $fiche, OffreInterface $offre)
    {
        if ($offre->getTelephone()) {
            $fiche->setTelephone($offre->getTelephone());
        }
        if ($offre->getFax()) {
            $fiche->setFax($offre->getFax());
        }
        if ($offre->getEmail()) {
            $fiche->setEmail($offre->getEmail());
        }
        if ($offre->getWebsite()) {
            $fiche->setWebsite($offre->getWebsite());
        }
        if ($offre->getRue()) {
            $fiche->setRue($offre->getRue());
        }
        if ($offre->getCodePostal()) {
            $fiche->setCp($offre->getCodePostal());
        }
        if ($offre->getLocalite()) {
            $fiche->setLocalite($offre->getLocalite());
        }
        if ($offre->getContactNom()) {
            $fiche->setNom($offre->getContactNom());
        }
        if ($offre->getCivilite()) {
            $fiche->setCivilite($offre->getCivilite());
        }
        if ($offre->getLatitude()) {
            $fiche->setLatitude($offre->getLatitude());
        }
        if ($offre->getLongitude()) {
            $fiche->setLongitude($offre->getLongitude());
        }
    }

    public function flush()
    {
        $this->ficheRepository->flush();
        $this->classementRepository->flush();
    }

    protected function setContacts(Fiche $fiche, $contacts)
    {
        if (!isset($contacts['ap']) && !isset($contacts['contact'])) {
            return;
        }

        $contactPrincipal = $contacts['ap'];
        $contactSecondaire = isset($contacts['contact']) ? $contacts['contact'] : null;

        $fiche->setCivilite($contactPrincipal['civilite']);
        $fiche->setNom($contactPrincipal['noms']);
        $fiche->setPrenom($contactPrincipal['prenoms']);
        $fiche->setRue($contactPrincipal['adresse']);
        $fiche->setNumero($contactPrincipal['numero']);
        $fiche->setCp($contactPrincipal['postal']);
        $fiche->setLocalite($contactPrincipal['localite_nom']);

        $communications = $contactPrincipal['communications'];
        $telephones = [];

        foreach ($communications as $communication) {
            $type = $communication['type'];
            $value = $communication['value'];

            if ($value) {
                //   echo $type . ' ' . $value . ' | ';
                switch ($type) {
                    case 'tel':
                        $telephones[] = $value;
                        break;
                    case 'tel_bur':
                        $fiche->setTelephoneAutre($value);
                        break;
                    case 'gsm':
                        $fiche->setGsm($value);
                        break;
                    case 'mail':
                        $fiche->setEmail($value);
                        break;
                    case 'url':
                        $fiche->setWebsite($value);
                        break;
                    case 'fax':
                        $fiche->setFax($value);
                        break;
                    case 'url_facebk':
                        $fiche->setFacebook($value);
                        break;
                    default:
                        break;
                }
            }
        }

        if (isset($telephones[0])) {
            $fiche->setTelephone($telephones[0]);
        }
        if (isset($telephones[1])) {
            $fiche->setGsm($telephones[1]);
        }
        if (isset($telephones[2])) {
            $fiche->setTelephoneAutre($telephones[2]);
        }

        if ($contactSecondaire) {
            $telephones = [];

            $fiche->setContactRue($contactSecondaire['adresse']);
            $fiche->setContactNum($contactSecondaire['numero']);
            $fiche->setContactCp($contactSecondaire['postal']);
            $fiche->setContactLocalite($contactSecondaire['localite_nom']);
            $communicationsSecondaire = $contactSecondaire['communications'];

            foreach ($communicationsSecondaire as $communication) {
                $type = $communication['type'];
                $value = $communication['value'];
                if ($value) {
                    //      echo $type . ' ' . $value . ' | ';
                    switch ($type) {
                        case 'tel':
                            $telephones[] = $value;
                            break;
                        case 'gsm':
                            $fiche->setContactGsm($value);
                            break;
                        case 'mail':
                            $fiche->setContactEmail($value);
                            break;
                        case 'tel_bur':
                            $fiche->setContactTelephoneAutre($value);
                            break;
                        case 'fax':
                            $fiche->setContactFax($value);
                            break;
                        default:
                            break;
                    }
                }
            }

            if (isset($telephones[0])) {
                $fiche->setContactTelephone($telephones[0]);
            }
            if (isset($telephones[1])) {
                $fiche->setContactGsm($telephones[1]);
            }
            if (isset($telephones[2])) {
                $fiche->setContactTelephoneAutre($telephones[2]);
            }
        }
    }

    protected function setDescriptions2(Fiche $fiche, $descriptions)
    {
        foreach ($descriptions as $description) {
            $lot = $description['lot'];
            $comment1 = $comment2 = $comment3 = '';

            switch ($lot) {
                case 'lot_descript':
                    $comment1 .= $description['texte_fr'];
                    break;
                case 'lot_tarif':
                    $comment3 .= $description['texte_fr'];
                    break;
                case 'lot_horaire':
                case 'lot_equip':
                    $comment2 .= $description['texte_fr'];
                    break;
                default:
                    break;
            }

            if ($comment1) {
                $fiche->setComment1($comment1);
            }

            if ($comment2) {
                $fiche->setComment1($comment2);
            }

            if ($comment3) {
                $fiche->setComment1($comment3);
            }
        }
    }
}
