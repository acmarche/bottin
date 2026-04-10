<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Models\Shop;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use function mime_content_type;
use function str_starts_with;

final class MediaForm
{
    public static function configureCreate(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('uploaded_file')
                    ->label('Fichier')
                    ->disk('public')
                    ->required()
                    ->maxSize(10240),
                TextInput::make('name')
                    ->label('Intitulé')
                    ->helperText('Non requis, utile comme légende')
                    ->maxLength(150),
                Toggle::make('is_main')
                    ->label('Principal')
                    ->helperText('Utilisé comme image principale')
                    ->default(false),
            ]);
    }

    public static function configureEdit(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Intitulé')
                    ->helperText('Non requis, utile comme légende')
                    ->maxLength(150),
                Toggle::make('is_main')
                    ->label('Principal')
                    ->helperText('Utilisé comme image principale'),
            ]);
    }

    public static function createAction(CreateAction $action): CreateAction
    {
        return $action
            ->schema(self::configureCreate(new Schema())->getComponents())
            ->using(function (array $data, $livewire) {
                $shop = method_exists($livewire, 'getOwnerRecord')
                    ? $livewire->getOwnerRecord()
                    : Shop::findOrFail($livewire->shopId);

                $path = Storage::disk('public')->path($data['uploaded_file']);
                $mimeType = mime_content_type($path) ?: '';
                $isImage = str_starts_with($mimeType, 'image/');
                $collection = $isImage ? 'images' : 'documents';

                $adder = $shop->addMedia($path)
                    ->usingName($data['name'] ?: $shop->company)
                    ->withCustomProperties(['is_main' => (bool) ($data['is_main'] ?? false)]);

                if ($isImage) {
                    $adder->withResponsiveImages();
                }

                $adder->toMediaCollection($collection, 'public');
            });
    }

    public static function editAction(EditAction $action): EditAction
    {
        return $action
            ->schema(self::configureEdit(new Schema())->getComponents())
            ->using(function (Media $record, array $data) {
                $record->name = $data['name'] ?: $record->model->company;
                $record->setCustomProperty('is_main', (bool) ($data['is_main'] ?? false));
                $record->save();
            });
    }
}
