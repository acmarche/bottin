<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Models\Media;
use App\Models\Shop;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class MediaForm
{
    public static function configure(Schema $schema, Model|Shop $owner): Schema
    {
        return $schema
            ->components([
                Hidden::make('mime_type'),
                Hidden::make('size'),
                TextInput::make('name')
                    ->label('Intitulé')
                    ->helperText('Non requis, utile comme légende')
                    ->maxLength(150),
                FileUpload::make('file_name')
                    ->label('Fichier')
                    ->disk('public')
                    ->directory(Media::BASE_PATH.$owner->getKey())
                    ->visibility('public')
                    ->required()
                    ->maxSize(10240)
                    ->downloadable()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state instanceof TemporaryUploadedFile) {
                            $set('mime_type', $state->getMimeType());
                            $set('size', $state->getSize());
                        }
                    }),
                Toggle::make('is_main')
                    ->label('Principal')
                    ->helperText('Utilisé comme image principale')
                    ->default(false),
            ]);
    }
}
