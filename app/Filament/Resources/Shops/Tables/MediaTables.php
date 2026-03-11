<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use App\Models\Media;
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
                    ->action(fn (Media $media) => Storage::disk('public')->download($media->file_name)),
                ImageColumn::make('file_name')
                    ->disk('public')
                    ->visibility('public')
                    ->state(fn (Media $record): string => $record->storagePath())
                    ->checkFileExistence(false)
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                    ]),
                IconColumn::make('is_main')
                    ->label('Principal')
                    ->boolean(),
                TextColumn::make('size')
                    ->label('Taille')
                    ->suffix('Ko'),
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
