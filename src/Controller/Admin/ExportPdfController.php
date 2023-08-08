<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Pdf\Factory\PdfFactory;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Export controller.
 */
#[Route(path: '/admin/export/pdf')]
#[IsGranted('ROLE_BOTTIN_ADMIN')]
class ExportPdfController extends AbstractController
{
    public function __construct(private readonly PdfFactory $pdfFactory)
    {
    }

    #[Route(path: '/fiche/{id}', name: 'bottin_admin_export_fiche_pdf', methods: ['GET'])]
    public function fichePdf(Fiche $fiche): PdfResponse
    {
        $html = $this->pdfFactory->fiche($fiche);
        //   return new Response($html);
        return $this->pdfFactory->sendResponse($html, $fiche->getSlug());
    }

    #[Route(path: '/category/{category}', name: 'bottin_admin_export_fiches_by_category_pdf', methods: ['GET'])]
    public function fichesPdf(Category $category): PdfResponse
    {
        $html = $this->pdfFactory->fichesByCategory($category);
        $this->pdfFactory->pdf->setOption('footer-right', '[page]/[toPage]');

        return $this->pdfFactory->sendResponse($html, $category->getSlug());
    }
}
