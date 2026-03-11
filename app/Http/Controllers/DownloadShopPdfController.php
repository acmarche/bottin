<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\PdfExport;
use App\Models\Shop;

final class DownloadShopPdfController
{
    public function __invoke(Shop $shop)
    {
        return PdfExport::exportShop($shop);
    }
}
