<?php

namespace AcMarche\Bottin\Pdf\Factory;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Twig\Environment;

class PdfFactory
{
    private Pdf $pdf;
    private CategoryService $categoryService;
    private ClassementRepository $classementRepository;
    private PathUtils $pathUtils;
    private Environment $environment;

    public function __construct(
        CategoryService $categoryService,
        Pdf $pdf,
        ClassementRepository $classementRepository,
        PathUtils $pathUtils,
        Environment $environment
    ) {
        $this->pdf = $pdf;
        $this->categoryService = $categoryService;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
        $this->environment = $environment;
    }

    public function fiche(Fiche $fiche): PdfResponse
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $html = $this->environment->render(
            '@AcMarcheBottin/admin/pdf/fiche.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );

        // return new Response($html);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            'bottin_'.$fiche->getSlug().'.pdf'
        );
    }

    public function fichesByCategory(Category $category): PdfResponse
    {
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        $html = $this->environment->render(
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
            'bottin_'.$category->getSlug().'.pdf'
        );
    }
}
