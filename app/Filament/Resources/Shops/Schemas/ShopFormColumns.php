<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Models\Locality;
use App\Models\Tag;
use App\Models\User;
use App\Repository\TagRepository;
use App\Services\BelgianAddressService;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use SalemAljebaly\FilamentMapPicker\MapPicker;

final class ShopFormColumns
{
    public static function generalColumns(): array
    {
        return [
            TextInput::make('company')
                ->label('Société')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('postal_code')
                ->label('Code postal')
                ->integer()
                ->live(onBlur: true),
            TextInput::make('city')
                ->label('Ville')
                ->maxLength(255)
                ->datalist(Locality::query()->orderBy('name')->pluck('name')->toArray()),
            TextInput::make('street')
                ->label('Rue')
                ->maxLength(255)
                ->live(debounce: 500)
                ->datalist(
                    fn (Get $get): array => BelgianAddressService::streetsByPostalCode(
                        $get->string('postal_code'),
                        $get->string('street')
                    )
                ),
            TextInput::make('number')
                ->label('Numéro')
                ->maxLength(255),
            TextInput::make('vat_number')
                ->label('Numéro de TVA')
                ->maxLength(255),
            Toggle::make('enabled')
                ->label('Actif')
                ->default(true)
                ->visible(fn () => auth()->user() instanceof User),
        ];
    }

    public static function contactColumns(): array
    {
        return [
            TextInput::make('phone')
                ->label('Téléphone')
                ->tel()
                ->maxLength(255),
            TextInput::make('phone_other')
                ->label('Autre téléphone')
                ->tel()
                ->maxLength(255),
            TextInput::make('fax')
                ->label('Fax')
                ->maxLength(255),
            TextInput::make('mobile')
                ->label('Mobile')
                ->tel()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->maxLength(255),
            TextInput::make('website')
                ->label('Site web')
                ->rule('url')
                ->maxLength(255),
        ];
    }

    public static function notesColumns(): array
    {
        return [
            Textarea::make('comment1')
                ->label('Commentaire 1')
                ->rows(4)
                ->columnSpanFull(),
            Textarea::make('comment2')
                ->label('Commentaire 2')
                ->rows(4)
                ->columnSpanFull(),
            Textarea::make('comment3')
                ->label('Commentaire 3')
                ->rows(4)
                ->columnSpanFull(),
            Textarea::make('note')
                ->label('Note')
                ->helperText('Ce champ n\'est pas visible par le public')
                ->rows(4)
                ->columnSpanFull()
                ->visible(fn () => auth()->user() instanceof User),
            Select::make('point_of_sale_id')
                ->label('Point de vente')
                ->relationship('pointOfSale', 'name')
                ->searchable()
                ->preload()
                ->visible(fn () => auth()->user() instanceof User),
        ];
    }

    public static function adminColumns(): array
    {
        return [
            TextInput::make('admin_civility')
                ->label('Civilité')
                ->maxLength(255),
            TextInput::make('admin_function')
                ->label('Fonction')
                ->maxLength(255),
            TextInput::make('admin_last_name')
                ->label('Nom')
                ->maxLength(255),
            TextInput::make('admin_first_name')
                ->label('Prénom')
                ->maxLength(255),
            TextInput::make('admin_phone')
                ->label('Téléphone')
                ->tel()
                ->maxLength(255),
            TextInput::make('admin_phone_other')
                ->label('Autre téléphone')
                ->tel()
                ->maxLength(255),
            TextInput::make('admin_fax')
                ->label('Fax')
                ->maxLength(255),
            TextInput::make('admin_mobile')
                ->label('Mobile')
                ->tel()
                ->maxLength(255),
            TextInput::make('admin_email')
                ->label('Email')
                ->email()
                ->maxLength(255),
        ];
    }

    public static function socialColumns(): array
    {
        return [
            TextInput::make('facebook')
                ->label('Facebook')
                ->rule('url')
                ->maxLength(255),
            TextInput::make('twitter')
                ->label('Twitter')
                ->rule('url')
                ->maxLength(255),
            TextInput::make('instagram')
                ->label('Instagram')
                ->rule('url')
                ->maxLength(255),
            TextInput::make('tiktok')
                ->label('TikTok')
                ->rule('url')
                ->maxLength(255),
            TextInput::make('youtube')
                ->label('YouTube')
                ->rule('url')
                ->maxLength(255),
            TextInput::make('linkedin')
                ->label('LinkedIn')
                ->rule('url')
                ->maxLength(255),
        ];
    }

    public static function mapColumns(): array
    {
        return [
            Hidden::make('latitude')
                ->rules(['nullable', 'numeric']),
            Hidden::make('longitude')
                ->rules(['nullable', 'numeric']),
            MapPicker::make('location')
                ->label('Localisation')
                ->latlngFields('latitude', 'longitude')
                ->searchable()
                ->collapsibleSearch()
                ->draggable()
                ->height(400)
                ->afterStateHydrated(function (MapPicker $component, $state, $record): void {
                    if ($record === null) {
                        return;
                    }

                    $lat = $record->latitude;
                    $lng = $record->longitude;

                    if ($lat !== null && $lng !== null) {
                        return;
                    }

                    $address = implode(' ', array_filter([
                        $record->street,
                        $record->number,
                        $record->postal_code,
                        $record->city,
                    ]));

                    if ($address === '') {
                        return;
                    }

                    try {
                        $response = Http::withHeaders([
                            'User-Agent' => 'Bottin/1.0',
                        ])->get('https://nominatim.openstreetmap.org/search', [
                            'q' => $address,
                            'format' => 'json',
                            'limit' => 1,
                        ]);

                        $results = $response->json();

                        if (! empty($results[0]['lat']) && ! empty($results[0]['lon'])) {
                            $component->getLivewire()->data['latitude'] = $results[0]['lat'];
                            $component->getLivewire()->data['longitude'] = $results[0]['lon'];
                        }
                    } catch (Throwable) {
                        // Geocoding failed, use default location
                    }
                })
                ->columnSpanFull(),
            TextInput::make('latitude_display')
                ->label('Latitude')
                ->disabled()
                ->dehydrated(false)
                ->afterStateHydrated(fn (TextInput $component, $record) => $component->state($record?->latitude)),
            TextInput::make('longitude_display')
                ->label('Longitude')
                ->disabled()
                ->dehydrated(false)
                ->afterStateHydrated(fn (TextInput $component, $record) => $component->state($record?->longitude)),
        ];
    }

    public static function contactPersonColumns(): array
    {
        return [
            TextInput::make('civility')
                ->label('Civilité')
                ->maxLength(255),
            TextInput::make('function')
                ->label('Fonction')
                ->maxLength(255),
            TextInput::make('last_name')
                ->label('Nom')
                ->maxLength(255),
            TextInput::make('first_name')
                ->label('Prénom')
                ->maxLength(255),
            TextInput::make('contact_street')
                ->label('Rue')
                ->maxLength(255),
            TextInput::make('contact_number')
                ->label('Numéro')
                ->maxLength(255),
            TextInput::make('contact_postal_code')
                ->label('Code postal')
                ->maxLength(255),
            TextInput::make('contact_city')
                ->label('Ville')
                ->maxLength(255),
            TextInput::make('contact_email')
                ->label('Email')
                ->email()
                ->maxLength(255),
            TextInput::make('contact_phone')
                ->label('Téléphone')
                ->tel()
                ->maxLength(255),
            TextInput::make('contact_phone_other')
                ->label('Autre téléphone')
                ->tel()
                ->maxLength(255),
            TextInput::make('contact_fax')
                ->label('Fax')
                ->maxLength(255),
            TextInput::make('contact_mobile')
                ->label('Mobile')
                ->tel()
                ->maxLength(255),
        ];
    }

    public static function tagColumns(): array
    {
        return [
            Select::make('tags')
                ->label('Tags')
                ->relationship(
                    name: 'tags',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn (Builder $query) => TagRepository::listTags($query),
                )
                ->getOptionLabelFromRecordUsing(fn (Tag $record): string => $record->tagGroup
                    ? "{$record->tagGroup->name} - {$record->name}"
                    : $record->name)
                ->multiple()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(255),
                    Select::make('tag_group_id')
                        ->label('Groupe')
                        ->relationship('tagGroup', 'name')
                        ->searchable()
                        ->preload(),
                ]),
        ];
    }
}
