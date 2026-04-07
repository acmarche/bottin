<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Shop;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

final class PdfExport
{
    public static function exportShop(Shop $shop): Responsable
    {
        $filename = 'shop-'.$shop->slug.'-'.time().'.pdf';

        $shop->load(['categories.parent.parent.parent.parent', 'schedules', 'tags', 'situations']);

        return Pdf::view('pdfs.shop', ['shop' => $shop])
            ->withBrowsershot(function (Browsershot $browsershot): void {
                if ($path = config('pdf.node_modules_path')) {
                    $browsershot->setNodeModulePath($path);
                }
                if ($path = config('pdf.chrome_path')) {
                    $browsershot->setChromePath($path);
                }
            })
            ->format('a4')
            ->name($filename);
    }

    public static function savePdf(string $filename)
    {
        $relativePath = 'pdf/'.$filename;

        Storage::disk('public')->makeDirectory('pdf');

        $fullPath = Storage::disk('public')->path($relativePath);
    }
}
