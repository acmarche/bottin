<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Export controller.
 *
 * @Route("/admin/export/pdf")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ExportPdfController extends AbstractController
{
    private PdfFactory $pdfFactory;

    public function __construct(PdfFactory $pdfFactory)
    {
        $this->pdfFactory = $pdfFactory;
    }

    /**
     * @Route("/fiche/{id}", name="bottin_admin_export_fiche_pdf", methods={"GET"})
     */
    public function fichePdf(Fiche $fiche): PdfResponse
    {
        return $this->pdfFactory->fiche($fiche);
    }

    /**
     * @Route("/category/{category}", name="bottin_admin_export_fiches_by_category_pdf", methods={"GET"})
     */
    public function fichesPdf(Category $category): PdfResponse
    {
        return $this->pdfFactory->fichesByCategory($category);
    }
}
