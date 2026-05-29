<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Actions\DeleteApiTokenAction;
use App\Filament\Actions\GenerateApiTokenAction;
use App\Filament\Actions\RegenerateApiTokenAction;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

final class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                GenerateApiTokenAction::make(),
                RegenerateApiTokenAction::make(),
                DeleteApiTokenAction::make(),
            ])
                ->label('Token API')
                ->icon('tabler-key')
                ->button(),
            DeleteAction::make()
                ->icon(Heroicon::Trash),
        ];
    }
}
