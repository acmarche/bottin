<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Export\CsvGenerator;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/admin/export/csv")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ExportCsvController extends AbstractController
{
    private CsvGenerator $csvGenerator;

    public function __construct(CsvGenerator $csvGenerator)
    {
        $this->csvGenerator = $csvGenerator;
    }

    /**
     * @Route("/category/{id}", name="bottin_admin_export_category_xls")
     * @Route("/categories", name="bottin_admin_export_categories_xls", methods={"GET"})
     */
    public function categoryXls(Category $category = null): BinaryFileResponse
    {
        $spreadsheet = $this->csvGenerator->categoryXSLObject($category);

        $xlsx = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'category.xls');

        // Create the excel file in the tmp directory of the system
        $xlsx->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, 'category.xls', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    /**
     * @Route("/fiche/{id}", name="bottin_admin_export_fiches_xls_by_category", methods={"GET"})
     * @Route("/fiches", name="bottin_admin_export_fiches_xls", methods={"GET"})
     *
     * @return BinaryFileResponse
     *
     * @throws Exception
     */
    public function fichesXls(?Category $category = null): BinaryFileResponse
    {
        $spreadsheet = $this->csvGenerator->ficheXSLObject($category);
        $spreadsheet->getProperties()->setCreator('intranet')
            ->setTitle('Liste des contacts');

        $xlsx = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'fiches.xls');

        // Create the excel file in the tmp directory of the system
        $xlsx->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, 'fiches.xls', ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
