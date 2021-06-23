<?php

namespace AcMarche\Bottin\Controller\Admin;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Service\CategoryService;
use AcMarche\Bottin\Utils\PathUtils;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
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
    private Pdf $pdf;
    private CategoryService $categoryService;
    private ClassementRepository $classementRepository;
    private PathUtils $pathUtils;

    public function __construct(
        CategoryService $categoryService,
        Pdf $pdf,
        ClassementRepository $classementRepository,
        PathUtils $pathUtils
    ) {
        $this->pdf = $pdf;
        $this->categoryService = $categoryService;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * @Route("/fiche/{id}", name="bottin_admin_export_fiche_pdf", methods={"GET"})
     */
    public function fichePdf(Fiche $fiche): PdfResponse
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $html = $this->renderView(
            '@AcMarcheBottin/admin/pdf/fiche.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );

        // return new Response($html);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            'bottin_' . $fiche->getSlug() . '.pdf'
        );
    }

    /**
     * @Route("/category/{category}", name="bottin_admin_export_fiches_by_category_pdf", methods={"GET"})
     */
    public function fichesPdf(Category $category): PdfResponse
    {
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        $html = $this->renderView(
            '@AcMarcheBottin/admin/pdf/category.html.twig',
            [
                'category' => $category,
                'fiches' => $fiches,
            ]
        );

        //return new Response($html);

        $this->pdf->setOption('footer-right', '[page]/[toPage]');

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            'bottin_' . $category->getSlug() . '.pdf'
        );
    }
}
