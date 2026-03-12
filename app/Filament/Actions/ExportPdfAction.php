<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Shop;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class ExportPdfAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label('Export en pdf')
            ->icon('tabler-pdf')
            ->url(fn (Shop $record) => route('export.shop', $record))
            ->action(function () {
                Notification::make()
                    ->title('Pdf exporté')
                    ->success()
                    ->send();
            });

    }

    public static function getDefaultName(): ?string
    {
        return 'exportPdf';
    }
}
