<?php

namespace AcMarche\Bottin\Command;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\Hades;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncFtlbCommand extends Command
{
    /**
     * @var Hades
     */
    private $hades;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var ClassementRepository
     */
    private $classementRepository;

    public function __construct(
        Hades $hades,
        CategoryRepository $categoryRepository,
        FicheRepository $ficheRepository,
        ClassementRepository $classementRepository
    ) {
        parent::__construct();
        $this->hades = $hades;
        $this->categoryRepository = $categoryRepository;
        $this->ficheRepository = $ficheRepository;
        $this->classementRepository = $classementRepository;
    }

    protected function configure()
    {
        $this
            ->setName('bottin:syncftlb')
            ->setDescription('Synchronise avec la ftlb');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->user = 'jfsenechal';

        $this->getChambres();
        $this->getCamping();
        $this->getHotels();
        $this->getGites();

        $this->categoryRepository->flush();

        $output->writeln('ok');
        return 0;
    }

    protected function getCamping()
    {
        $categorie = $this->categoryRepository->find(652);
        if (!$categorie) {
            return;
        }

        $offres = $this->hades->getOffres('cat_id=camp_non_rec,camping');

        foreach ($offres as $offre) {
            $this->traitementOffre($offre, $categorie);
        }
    }

    protected function getHotels()
    {
        $categorie = $this->categoryRepository->find(649);
        if (!$categorie) {
            return;
        }

        $offres = $this->hades->getOffres('cat_id=hotel');

        foreach ($offres as $offre) {
            $this->traitementOffre($offre, $categorie);
        }
    }

    protected function getChambres()
    {
        $categorie = $this->categoryRepository->find(651);
        if (!$categorie) {
            return;
        }

        $offres = $this->hades->getOffres('cat_id=chbre_chb,chbre_hote');

        foreach ($offres as $offre) {
            $this->traitementOffre($offre, $categorie);
        }
    }

    protected function getGites()
    {
        $categorie = $this->categoryRepository->find(650);
        if (!$categorie) {
            return;
        }

        $offres = $this->hades->getOffres('cat_id=git_ferme,git_citad,git_big_cap,git_rural,mbl_trm,mbl_vac');

        foreach ($offres as $offre) {
            $this->traitementOffre($offre, $categorie);
        }
    }

    protected function traitementOffre($offre, Category $category)
    {
        $titre = (string) $offre->titre;
        $id = (int) $offre->attributes()->id;
        $localisation = $this->hades->getLocalisations($offre);
        $descriptions = $this->hades->getDescriptions($offre);
        $contacts = $this->hades->getContacts($offre);

        $fiche = $this->ficheRepository->findOneBy(['ftlb' => $id]);
        if (!$fiche) {
            $fiche = new Fiche();
            $fiche->setClef(bin2hex(random_bytes(16)));
            $fiche->setUser($this->user);
            $classement = new Classement();
            $classement->setCategory($category);
            $classement->setFiche($fiche);
            $classement->setPrincipal(true);
            $this->ficheRepository->persist($fiche);
            $this->classementRepository->persist($classement);
        }

        $this->output->writeln($titre);

        if (count($contacts) > 0) {
            $this->setContacts($fiche, $contacts);
        }

        if (count($descriptions) > 0) {
            $this->setDescriptions($fiche, $descriptions);
        }

        $fiche->setFtlb($id);
        $fiche->setSociete($titre);
        $fiche->setLatitude($localisation['latitude']);
        $fiche->setLongitude($localisation['longitude']);
        $fiche->setLocalite($localisation['localite_nom']);
        $fiche->setCp($localisation['code_postal']);

        $this->classementRepository->flush();
        $this->ficheRepository->flush();
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

    protected function setDescriptions(Fiche $fiche, $descriptions)
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
                case 'lot_equip':
                    $comment2 .= $description['texte_fr'];
                    break;
                case 'lot_horaire':
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
