<?php

namespace AcMarche\Bottin\Utils;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Contracts\Service\Attribute\Required;

trait PdfDownloaderTrait
{
    public Pdf $pdf;

    #[Required]
    public function setPdf(Pdf $pdf): void
    {
        $this->pdf = $pdf;
    }

    public function getPdf(): Pdf
    {
        return $this->pdf;
    }

    public function downloadPdf(string $html, string $fileName): PdfResponse
    {
        //debug
        // return new Response($html);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $fileName
        );
    }
}
