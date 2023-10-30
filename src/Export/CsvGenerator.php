<?php

namespace AcMarche\Bottin\Export;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Pdv;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\SelectionRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Bundle\SecurityBundle\Security;

class CsvGenerator
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly SelectionRepository $selectionRepository,
        private readonly Security $security,
        private readonly ExportUtils $exportUtils
    ) {
    }

    public function categoryXSLObject(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $user = $this->security->getUser();
        $selections = $this->selectionRepository->findByUser($user->getUserIdentifier());
        $categories = [];
        foreach ($selections as $selection) {
            $categories[] = $selection->category;
        }

        $ligne = 1;

        /**
         * titre des colonnes.
         */
        $colonnes = ['Niveau 0', 'Niveau 1', 'Niveau 2', 'Niveau 3', 'Identifiant'];

        $lettre = 'A';
        foreach ($colonnes as $colonne) {
            // $sheet->getColumnDimension('A')->setWidth(20);
            $worksheet->setCellValue($lettre.$ligne, $colonne);
            //    $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            ++$lettre;
        }

        ++$ligne;

        foreach ($categories as $categorie) {
            $lettre = 'A';
            $name = $categorie->name;
            $worksheet->setCellValue($lettre++.$ligne, $name);
            $worksheet->setCellValue('E'.$ligne, $categorie->getId());

            $children = $this->categoryRepository->getFlatTree($categorie->getRealMaterializedPath());
            foreach ($children as $child) {
                ++$ligne;
                $lettre = 'B';
                $childName = $child->name;
                $worksheet->setCellValue($lettre.$ligne, $childName);
                $worksheet->setCellValue('E'.$ligne, $child->getId());
                $enfants = $this->categoryRepository->getFlatTree($child->getRealMaterializedPath());
                foreach ($enfants as $enfant) {
                    ++$ligne;
                    $lettre = 'C';
                    $worksheet->setCellValue($lettre.$ligne, $enfant->name);
                    $worksheet->setCellValue('E'.$ligne, $enfant->getId());
                    $lasts = $this->categoryRepository->getFlatTree($enfant->getRealMaterializedPath());
                    foreach ($lasts as $last) {
                        ++$ligne;
                        $lettre = 'D';
                        $worksheet->setCellValue($lettre.$ligne, $last->name);
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
            'tva/bce',
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
            // $sheet->getColumnDimension('A')->setWidth(20);
            $worksheet->setCellValue($lettre.$ligne, $colonne);
            //  $sheet->getStyle($lettre.$ligne)->applyFromArray($font);
            ++$lettre;
        }

        ++$ligne;
        foreach ($fiches as $fiche) {
            $pdv = $fiche->getPdv() instanceof Pdv ? $fiche->pdv->intitule : '';

            $lettre = 'A';
            $worksheet->setCellValue($lettre++.$ligne, $fiche->societe);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->rue);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->numero);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->cp);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->localite);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->telephone);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->telephone_autre);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->gsm);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->fax);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->email);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->website);
            /*
             * Infos
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->centreville);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->midi);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->pmr);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->ecommerce);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->numero_tva);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->click_collect);
            $worksheet->setCellValue($lettre++.$ligne, $pdv);
            /*
             * CONTACT
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->nom);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->prenom);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->fonction);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_rue);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_num);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_cp);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_localite);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_telephone);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_telephone_autre);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_gsm);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_fax);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->contact_email);
            /*
             * Administrateur
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_civilite);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_nom);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_prenom);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_fonction);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_telephone);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_telephone_autre);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_fax);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_gsm);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->admin_email);
            /*
             * Sociaux
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->facebook);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->twitter);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->instagram);
            /*
             * Commentaires
             */
            $worksheet->setCellValue($lettre++.$ligne, $fiche->comment1);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->comment2);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->comment3);
            $worksheet->setCellValue($lettre++.$ligne, $fiche->note);
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
            $category = $classement->category;
            $worksheet->setCellValue($lettre++.$ligne, $category->name);
            ++$lettre;
        }
    }
}
