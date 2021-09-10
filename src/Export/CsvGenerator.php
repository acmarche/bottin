<?php

namespace AcMarche\Bottin\Export;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\SelectionRepository;
use AcMarche\Bottin\Category\Repository\CategoryService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\Security\Core\Security;

class CsvGenerator
{
    private CategoryRepository $categoryRepository;
    private CategoryService $categoryService;
    private FicheRepository $ficheRepository;
    private SelectionRepository $selectionRepository;
    private Security $security;
    private ExportUtils $exportUtils;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryService $categoryService,
        FicheRepository $ficheRepository,
        SelectionRepository $selectionRepository,
        Security $security,
        ExportUtils $exportUtils
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
        $this->ficheRepository = $ficheRepository;
        $this->selectionRepository = $selectionRepository;
        $this->security = $security;
        $this->exportUtils = $exportUtils;
    }

    public function categoryXSLObject(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $user = $this->security->getUser();
        $selections = $this->selectionRepository->findByUser($user->getUserIdentifier());
        $categories = [];
        foreach ($selections as $selection) {
            $categories[] = $selection->getCategory();
        }

        $ligne = 1;

        /**
         * titre des colonnes.
         */
        $colonnes = ['Niveau 0', 'Niveau 1', 'Niveau 2', 'Niveau 3', 'Identifiant'];

        $lettre = 'A';
        foreach ($colonnes as $colonne) {
            //$sheet->getColumnDimension('A')->setWidth(20);
            $worksheet->setCellValue($lettre.$ligne, $colonne);
            //    $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            ++$lettre;
        }

        ++$ligne;

        foreach ($categories as $categorie) {
            $lettre = 'A';
            $name = $categorie->getName();
            $worksheet->setCellValue($lettre++.$ligne, $name);
            $worksheet->setCellValue('E'.$ligne, $categorie->getId());

            $children = $this->categoryRepository->getFlatTree($categorie->getRealMaterializedPath());
            foreach ($children as $child) {
                ++$ligne;
                $lettre = 'B';
                $childName = $child->getName();
                $worksheet->setCellValue($lettre.$ligne, $childName);
                $worksheet->setCellValue('E'.$ligne, $child->getId());
                $enfants = $this->categoryRepository->getFlatTree($child->getRealMaterializedPath());
                foreach ($enfants as $enfant) {
                    ++$ligne;
                    $lettre = 'C';
                    $worksheet->setCellValue($lettre.$ligne, $enfant->getName());
                    $worksheet->setCellValue('E'.$ligne, $enfant->getId());
                    $lasts = $this->categoryRepository->getFlatTree($enfant->getRealMaterializedPath());
                    foreach ($lasts as $last) {
                        ++$ligne;
                        $lettre = 'D';
                        $worksheet->setCellValue($lettre.$ligne, $last->getName());
                        $worksheet->setCellValue('E'.$ligne, $last->getId());
                    }
                }
            }
            ++$ligne;
        }

        return $spreadsheet;
    }

    public function ficheXSLObject(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getDefaultRowDimension()->setRowHeight(15);

        $user = $this->security->getUser();
        $fiches = $this->exportUtils->getFichesBySelection($user->getUserIdentifier());

        $font = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                //     'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                //     'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ],
        ];

        /**
         * titre des colonnes.
         */
        $colonnes = [
            'Societe',
            'rue',
            'numero',
            'cp',
            'localite',
            'telephone',
            'telephoneAutre',
            'gsm',
            'Fax',
            'email',
            'site',
            'centreVille',
            'midi',
            'pmr',
            'ecommerce',
            'ClickAndCollect',
            'pdv',
            'Contactnom',
            'Contactprenom',
            'Contactfonction',
            'ContactRue',
            'ContactNum',
            'ContactCp',
            'ContactLocalite',
            'ContactTelephone',
            'ContactTelephoneAutre',
            'ContactGsm',
            'ContactFax',
            'ContactEmail',
            'AdminCivlite',
            'AdminNom',
            'AdminPrenom',
            'AdminFonction',
            'AdminTelephone',
            'AdminTelephoneAutre',
            'AdminFax',
            'AdminGsm',
            'AdminEmail',
            'facebook',
            'twitter',
            'Instagram',
            'Comment1',
            'Comment2',
            'Comment3',
            'Note',
            'Updated',
            'Classements',
        ];

        $ligne = 1;
        $lettre = 'A';
        foreach ($colonnes as $colonne) {
            //$sheet->getColumnDimension('A')->setWidth(20);
            $worksheet->setCellValue($lettre.$ligne, $colonne);
            //  $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            ++$lettre;
        }

        ++$ligne;
        foreach ($fiches as $fiche) {
            $pdv = null !== $fiche->getPdv() ? $fiche->getPdv()->getIntitule() : '';

            $lettre = 'A';
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getSociete());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getRue());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getNumero());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getCp());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getLocalite());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getTelephone());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getTelephoneAutre());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getGsm());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getFax());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getEmail());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getWebsite());
            /*
             * Infos
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getCentreville());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getMidi());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getPmr());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->isEcommerce());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->isClickCollect());
            $worksheet->setCellValue($lettre++.$ligne, $pdv);
            /*
             * CONTACT
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getNom());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getPrenom());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getFonction());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactRue());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactNum());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactCp());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactLocalite());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactTelephone());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactTelephoneAutre());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactGsm());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactFax());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getContactEmail());
            /*
             * Administrateur
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminCivilite());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminNom());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminPrenom());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminFonction());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminTelephone());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminTelephoneAutre());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminFax());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminGsm());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getAdminEmail());
            /*
             * Sociaux
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getFacebook());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getTwitter());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getInstagram());
            /*
             * Commentaires
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getComment1());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getComment2());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getComment3());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getNote());
            $worksheet->setCellValue($lettre++.$ligne, $fiche->getUpdatedAt()->format('d-m-Y'));

            $this->addClassements($fiche, $worksheet, $lettre, $ligne);

            ++$ligne;
        }

        return $spreadsheet;
    }

    protected function addClassements(Fiche $fiche, Worksheet $worksheet, $lettre, $ligne): void
    {
        $classements = $fiche->getClassements();

        foreach ($classements as $classement) {
            $category = $classement->getCategory();
            $worksheet->setCellValue($lettre++.$ligne, $category->getName());
            ++$lettre;
        }
    }
}
