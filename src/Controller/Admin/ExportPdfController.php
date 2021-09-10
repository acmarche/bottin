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
        $html = $this->pdfFactory->fiche($fiche);

        return $this->pdfFactory->sendResponse($html, $fiche->getSlug());
    }

    /**
     * @Route("/category/{category}", name="bottin_admin_export_fiches_by_category_pdf", methods={"GET"})
     */
    public function fichesPdf(Category $category): PdfResponse
    {
        $html = $this->pdfFactory->fichesByCategory($category);
        $this->pdfFactory->pdf->setOption('footer-right', '[page]/[toPage]');

        return $this->pdfFactory->sendResponse($html, $category->getSlug());
    }
}
