<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Repository\FicheRepository;
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
 * @Route("/export/pdf")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ExportPdfController extends AbstractController
{
    /**
     * @var Pdf
     */
    private $pdf;
    /**
     * @var CategoryService
     */
    private $categoryService;
    /**
     * @var FicheRepository
     */
    private $ficheRepository;
    /**
     * @var ClassementRepository
     */
    private $classementRepository;
    /**
     * @var PathUtils
     */
    private $pathUtils;

    public function __construct(
        CategoryService $categoryService,
        Pdf $pdf,
        ClassementRepository $classementRepository,
        PathUtils $pathUtils,
        FicheRepository $ficheRepository
    ) {
        $this->pdf = $pdf;
        $this->categoryService = $categoryService;
        $this->ficheRepository = $ficheRepository;
        $this->classementRepository = $classementRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * @Route("/fiche/{id}", name="bottin_export_fiche_pdf", methods={"GET"})
     */
    public function fichePdf(Fiche $fiche)
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $html = $this->renderView(
            '@AcMarcheBottin/pdf/fiche.html.twig',
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

    /**
     * @Route("/category/{category}", name="bottin_export_fiches_by_category_pdf", methods={"GET"})
     */
    public function fichesPdf(Category $category)
    {
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        $html = $this->renderView(
            '@AcMarcheBottin/pdf/category.html.twig',
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

    /**
     * @Route("/category/{id}", name="bottin_export_category_pdf", methods={"GET"})
     * @Route("/categories", name="bottin_export_categories_pdf", methods={"GET"})
     */
    public function rubriquePdf(Category $category = null)
    {
        $name = 'all';

        if ($category) {
            $name = $category->getId();
        }

        $html = $this->renderView(
            '@AcMarcheBottin/pdf/head.html.twig',
            [
                'title' => 'all',
            ]
        );

        $children = $category->getChildren();
        foreach ($children as $child) {
            $level = 1; //pour chapitre pdf

            $html .= $this->renderView(
                '@AcMarcheBottin/pdf/category/head.html.twig',
                [
                    'entity' => $child,
                    'level' => $level,
                ]
            );
            $enfants = $child['__children'];
            foreach ($enfants as $enfant) {
                $level = 2; //pour chapitre pdf

                $html .= $this->renderView(
                    '@AcMarcheBottin/pdf/category/head.html.twig',
                    [
                        'entity' => $enfant,
                        'level' => $level,
                    ]
                );
            }
        }

        $html .= $this->renderView('@AcMarcheBottin/pdf/foot.html.twig', []);

        //   print_r($html);
        //   return array();
        //   exit();

        $this->pdf->setOption('footer-right', '[page]/[toPage]');

        /* if (count($dates) > 6) {
          $snappy->setOption('orientation', 'landscape');
          } */

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            'bottin_'.$name.'.pdf'
        );
    }

}
