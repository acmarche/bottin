<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function str_starts_with;

final class MediaTables
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([
                TextColumn::make('download')
                    ->label('Téléchargement')
                    ->state('Télécharger')
                    ->icon('tabler-download')
                    ->action(fn (Media $media) => Storage::disk('public')->download(
                        $media->getPathRelativeToRoot()
                    )),
                ImageColumn::make('preview')
                    ->label('Aperçu')
                    ->disk('public')
                    ->state(fn (Media $record): ?string => str_starts_with($record->mime_type, 'image/') ? $record->getPathRelativeToRoot() : null)
                    ->checkFileExistence(false)
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ]),
                IconColumn::make('document_icon')
                    ->label('Type')
                    ->state(fn (Media $record): bool => ! str_starts_with($record->mime_type, 'image/'))
                    ->trueIcon('tabler-file-type-pdf')
                    ->falseIcon(false)
                    ->boolean(),
                IconColumn::make('is_main')
                    ->label('Principal')
                    ->state(fn (Media $record): bool => (bool) $record->getCustomProperty('is_main', false))
                    ->falseIcon(false)
                    ->boolean(),
                TextColumn::make('size')
                    ->label('Taille')
                    ->suffix('Ko'),
                TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge(),
                TextColumn::make('mime_type'),
            ])
            ->defaultPaginationPageOption(50)
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
