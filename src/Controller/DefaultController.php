<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Service\CategoryService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Default controller.
 *
 *
 */
class DefaultController extends AbstractController
{
    /**
     * @var CategoryService
     */
    private $categoryService;
    private $prop;
    /**
     * @var Pdf
     */
    private $pdf;

    public function __construct(
        CategoryService $categoryService,
        Pdf $pdf
    ) {
        $this->categoryService = $categoryService;
        $this->pdf = $pdf;
    }

    /**
     * @Route("/", name="bottin_home")
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
    public function index()
    {
        return $this->render(
            '@AcMarcheBottin/default/index.html.twig'
        );
    }

    /**
     * @Route("/empty", name="bottin_categories_empty")
     * @IsGranted("ROLE_BOTTIN_ADMIN")
     */
    public function empty()
    {
        $categories = $this->categoryService->getEmpyCategories();

        return $this->render(
            '@AcMarcheBottin/default/empty.html.twig',
            [
                'categories' => $categories,
            ]
        );
    }

    /**
     * @Route("/cv/{pdf}", name="bottin_cv")
     * @param bool $pdf
     * @return PdfResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function cv(bool $pdf = false)
    {
        if ($pdf) {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $html = $this->renderView('@AcMarcheBottin/cv/cv.html.twig');
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4');
            $dompdf->render();

            return new PdfResponse(
                $this->pdf->getOutputFromHtml(
                    $html,
                    [
                        'viewport-size' => '1024x768',
                        'page-size' => 'A4',
                    ]
                ),
                'jfsenechal-cv.pdf'
            );

            return new BinaryFileResponse(
                $dompdf->stream(
                    "mypdf.pdf",
                    [
                        "Attachment" => true,
                    ]
                )
            );
        }

        return $this->render('@AcMarcheBottin/cv/cv.html.twig');
    }

    /**
     * @Route("/checkup", name="bottin_checkup")
     */
    public function checkup()
    {
        $this->prop->findAll();

        return $this->render(
            '@AcMarcheBottin/default/index.html.twig'
        );
    }

    public function generate(
        $name,
        $parameters = [],
        $absolute = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        if ('sylius_shop_product_index' === $name) {
            $redirectRoute = $this->getTaxonRedirectRoute($parameters);

            if (null !== $redirectRoute) {
                return $redirectRoute;
            }
        }

        return $this->router->generate($name, $parameters, $absolute);
    }
}
