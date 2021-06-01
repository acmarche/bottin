<?php

namespace AcMarche\Bottin\Export;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Service\CategoryService;
use AcMarche\Bottin\Utils\SortUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CsvGenerator
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryService $categoryService,
        FicheRepository $ficheRepository
    ) {

        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
        $this->ficheRepository = $ficheRepository;
    }

    public function categoryXSLObject(Category $category = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!$category) {
            $categories = $this->categoryRepository->getRootNodes();
        } else {
            $categories = $this->categoryRepository->search(null, $category);
        }

        $ligne = 1;

        /**
         * titre des colonnes.
         */
        $colonnes = ['Niveau 0', 'Niveau 1', 'Niveau 2', 'Niveau 3', 'Identifiant'];

        $lettre = 'A';
        foreach ($colonnes as $colonne) {
            //$sheet->getColumnDimension('A')->setWidth(20);
            $sheet->setCellValue($lettre.$ligne, $colonne);
            //    $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            ++$lettre;
        }

        ++$ligne;

        foreach ($categories as $categorie) {
            $lettre = 'A';
            $name = $categorie->getName();
            $sheet->setCellValue($lettre++.$ligne, $name);
            $sheet->setCellValue('E'.$ligne, $categorie->getId());

            $children = $this->categoryRepository->getFlatTree($categorie->getRealMaterializedPath());
            foreach ($children as $child) {
                ++$ligne;
                $lettre = 'B';
                $childName = $child->getName();
                $sheet->setCellValue($lettre.$ligne, $childName);
                $sheet->setCellValue('E'.$ligne, $child->getId());
                $enfants = $this->categoryRepository->getFlatTree($child->getRealMaterializedPath());
                foreach ($enfants as $enfant) {
                    ++$ligne;
                    $lettre = 'C';
                    $sheet->setCellValue($lettre.$ligne, $enfant->getName());
                    $sheet->setCellValue('E'.$ligne, $enfant->getId());
                    $lasts = $this->categoryRepository->getFlatTree($enfant->getRealMaterializedPath());
                    foreach ($lasts as $last) {
                        ++$ligne;
                        $lettre = 'D';
                        $sheet->setCellValue($lettre.$ligne, $last->getName());
                        $sheet->setCellValue('E'.$ligne, $last->getId());
                    }
                }
            }
            ++$ligne;
        }

        return $spreadsheet;
    }

    public function ficheXSLObject(?Category $category = null): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultRowDimension()->setRowHeight(15);

        if ($category) {
            $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);
        } else {
            $fiches = $this->ficheRepository->findAll();
        }

        $fiches = SortUtils::sortFiche($fiches);

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
            $sheet->setCellValue($lettre.$ligne, $colonne);
            //  $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            ++$lettre;
        }

        ++$ligne;
        foreach ($fiches as $fiche) {
            $pdv = $fiche->getPdv() ? $fiche->getPdv()->getIntitule() : '';

            $lettre = 'A';
            $sheet->setCellValue($lettre++.$ligne, $fiche->getSociete());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getRue());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getNumero());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getCp());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getLocalite());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getTelephone());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getTelephoneAutre());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getGsm());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getFax());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getEmail());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getWebsite());
            /*
             * Infos
             */
            $sheet->setCellValue($lettre++.$ligne, $fiche->getCentreville());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getMidi());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getPmr());
            $sheet->setCellValue($lettre++.$ligne, $fiche->isEcommerce());
            $sheet->setCellValue($lettre++.$ligne, $fiche->isClickCollect());
            $sheet->setCellValue($lettre++.$ligne, $pdv);
            /*
             * CONTACT
             */
            $sheet->setCellValue($lettre++.$ligne, $fiche->getNom());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getPrenom());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getFonction());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactRue());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactNum());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactCp());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactLocalite());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactTelephone());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactTelephoneAutre());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactGsm());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactFax());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getContactEmail());
            /*
             * Administrateur
             */
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminCivilite());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminNom());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminPrenom());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminFonction());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminTelephone());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminTelephoneAutre());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminFax());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminGsm());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getAdminEmail());
            /*
             * Sociaux
             */
            $sheet->setCellValue($lettre++.$ligne, $fiche->getFacebook());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getTwitter());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getInstagram());
            /*
             * Commentaires
             */
            $sheet->setCellValue($lettre++.$ligne, $fiche->getComment1());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getComment2());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getComment3());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getNote());
            $sheet->setCellValue($lettre++.$ligne, $fiche->getUpdatedAt()->format('d-m-Y'));

            $this->addClassements($fiche, $sheet, $lettre, $ligne);

            ++$ligne;
        }

        return $spreadsheet;
    }

    protected function addClassements(Fiche $fiche, Worksheet $sheet, $lettre, $ligne)
    {
        $classements = $fiche->getClassements();

        foreach ($classements as $classement) {
            $category = $classement->getCategory();
            $sheet->setCellValue($lettre++.$ligne, $category->getName());
            ++$lettre;
        }
    }
}
