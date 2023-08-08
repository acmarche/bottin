<?php

namespace AcMarche\Bottin\Pdf\Factory;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Entity\Category;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Twig\Environment;

class PdfFactory
{
    public function __construct(private readonly CategoryService $categoryService, public Pdf $pdf, private readonly ClassementRepository $classementRepository, private readonly PathUtils $pathUtils, private readonly Environment $environment, private readonly ClassementHandler $classementHandler)
    {
    }

    public function fiche(Fiche $fiche): string
    {
        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);

        $fiche->classementsFull = $classements;

        return $this->environment->render(
            '@AcMarcheBottin/admin/pdf/fiche.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
            ]
        );

        // return new Response($html);
    }

    /**
     * @param array|Fiche[] $fiches
     */
    public function fichesPublipostage(array $fiches): string
    {
        foreach ($fiches as $fiche) {
            $classements = $this->classementRepository->getByFiche($fiche);
            if ([] !== $classements) {
                $fiche->root = $this->classementHandler->getRoot($classements[0]);
            }

            $classements = $this->pathUtils->setPathForClassements($classements);
            $fiche->classementsFull = $classements;
        }

        return $this->environment->render(
            '@AcMarcheBottin/admin/pdf/fiches_publipostage.html.twig',
            [
                'fiches' => $fiches,
            ]
        );
    }

    public function fichesByCategory(Category $category): string
    {
        $fiches = $this->categoryService->getFichesByCategoryAndHerChildren($category);

        return $this->environment->render(
            '@AcMarcheBottin/admin/pdf/category.html.twig',
            [
                'category' => $category,
                'fiches' => $fiches,
            ]
        );

        // return new Response($html);
    }

    public function sendResponse(string $html, string $name): PdfResponse
    {
        $this->pdf->setTimeout(3000);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            'bottin_'.$name.'.pdf'
        );
    }
}
