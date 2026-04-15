<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Models\Shop;
use App\Models\Token;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

final class ShopInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $columns = 2;
        if (auth()->user() instanceof User) {
            $columns = 1;
        }

        return $schema
            ->schema([
                Flex::make([
                    Grid::make($columns)
                        ->schema([
                            self::address(),
                            self::contact(),
                            self::contactPerson(),
                            self::social(),
                            self::notes(),
                            self::adminContact(),
                        ]),
                    Grid::make(1)
                        ->visible(fn (): bool => auth()->user() instanceof User)
                        ->schema([
                            Section::make('Etat')
                                ->label(null)
                                ->schema(self::status()),
                            self::tags(),
                            self::timestamps(),
                            self::token(),
                        ])
                        ->grow(false),
                ])->from('md')
                    ->columnSpanFull(),
            ]);
    }

    private static function status(): array
    {
        return [
            TextEntry::make('vat_number')
                ->label('Numéro de TVA'),
            TextEntry::make('slug')
                ->label('Slug'),
        ];
    }

    private static function tags(): Section
    {
        return Section::make('Tags')
            ->icon(Heroicon::Tag)
            ->schema([
                TextEntry::make('tags.name')
                    ->label(null)
                    ->hiddenLabel()
                    ->badge()
                    ->formatStateUsing(function (string $state, Shop $record): string {
                        $tag = $record->tags->firstWhere('name', $state);

                        return $tag?->tagGroup
                            ? "{$tag->tagGroup->name} - {$state}"
                            : $state;
                    })
                    ->placeholder('Aucun tag'),
            ]);
    }

    private static function address(): Section
    {
        return Section::make('Adresse')
            ->label(null)
            ->icon(Heroicon::MapPin)
            ->columns(2)
            ->schema([
                TextEntry::make('street')
                    ->label('Rue'),
                TextEntry::make('number')
                    ->label('Numéro'),
                TextEntry::make('postal_code')
                    ->label('Code postal'),
                TextEntry::make('city')
                    ->label('Ville'),
                TextEntry::make('longitude')
                    ->label('Longitude'),
                TextEntry::make('latitude')
                    ->label('Latitude'),
            ]);
    }

    private static function contact(): Section
    {
        return Section::make('Contact')
            ->label(null)
            ->icon(Heroicon::Phone)
            ->columns(2)
            ->schema([
                TextEntry::make('phone')
                    ->label('Téléphone'),
                TextEntry::make('phone_other')
                    ->label('Autre téléphone'),
                TextEntry::make('mobile')
                    ->label('Mobile'),
                TextEntry::make('email')
                    ->label('Email')
                    ->icon(Heroicon::Envelope),
                TextEntry::make('website')
                    ->label('Site web')
                    ->icon(Heroicon::GlobeAlt)
                    ->url(fn ($state): ?string => $state),
                TextEntry::make('vat_number')
                    ->label('Numéro de TVA')
                    ->visible(fn (): bool => auth()->user() instanceof Token),
            ]);
    }

    private static function social(): Section
    {
        return Section::make('Réseaux sociaux')
            ->label(null)
            ->icon(Heroicon::GlobeAlt)
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('facebook')
                            ->label('Facebook')
                            ->url(fn ($state): ?string => $state),
                        TextEntry::make('twitter')
                            ->label('Twitter')
                            ->url(fn ($state): ?string => $state),
                        TextEntry::make('instagram')
                            ->label('Instagram')
                            ->url(fn ($state): ?string => $state),
                        TextEntry::make('tiktok')
                            ->label('TikTok')
                            ->url(fn ($state): ?string => $state),
                        TextEntry::make('youtube')
                            ->label('YouTube')
                            ->url(fn ($state): ?string => $state),
                        TextEntry::make('linkedin')
                            ->label('LinkedIn')
                            ->url(fn ($state): ?string => $state),
                    ]),
            ]);
    }

    private static function contactPerson(): Section
    {
        return Section::make('Personne de contact')
            ->label(null)
            ->icon(Heroicon::User)
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->schema([
                TextEntry::make('civility')
                    ->label('Civilité'),
                TextEntry::make('function')
                    ->label('Fonction'),
                TextEntry::make('last_name')
                    ->label('Nom'),
                TextEntry::make('first_name')
                    ->label('Prénom'),
                TextEntry::make('contact_street')
                    ->label('Rue'),
                TextEntry::make('contact_number')
                    ->label('Numéro'),
                TextEntry::make('contact_postal_code')
                    ->label('Code postal'),
                TextEntry::make('contact_city')
                    ->label('Ville'),
                TextEntry::make('contact_phone')
                    ->label('Téléphone'),
                TextEntry::make('contact_phone_other')
                    ->label('Autre téléphone'),
                TextEntry::make('contact_mobile')
                    ->label('Mobile'),
                TextEntry::make('contact_email')
                    ->label('Email')
                    ->icon(Heroicon::Envelope),
            ]);
    }

    private static function adminContact(): Section
    {
        return Section::make('Contact administratif')
            ->label(null)
            ->icon(Heroicon::ShieldCheck)
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('admin_civility')
                            ->label('Civilité'),
                        TextEntry::make('admin_function')
                            ->label('Fonction'),
                        TextEntry::make('admin_last_name')
                            ->label('Nom'),
                        TextEntry::make('admin_first_name')
                            ->label('Prénom'),
                        TextEntry::make('admin_phone')
                            ->label('Téléphone'),
                        TextEntry::make('admin_phone_other')
                            ->label('Autre téléphone'),
                        TextEntry::make('admin_mobile')
                            ->label('Mobile'),
                        TextEntry::make('admin_email')
                            ->label('Email')
                            ->icon(Heroicon::Envelope),
                    ]),
            ]);
    }

    private static function timestamps(): Section
    {
        return Section::make('Dates')
            ->icon(Heroicon::Clock)
            ->columns(2)
            ->schema([
                TextEntry::make('user')
                    ->label('Ajouté par')
                    ->placeholder('—'),
                TextEntry::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i'),
            ]);
    }

    private static function token(): Section
    {
        return Section::make('Token commerçant')
            ->icon(Heroicon::Key)
            ->visible(fn (): bool => auth()->user() instanceof User)
            ->schema([
                TextEntry::make('token.expire_at')
                    ->label('Expiration')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                TextEntry::make('token_status')
                    ->label('Statut')
                    ->state(fn (Shop $record): string => match (true) {
                        $record->token === null => 'Aucun',
                        $record->token->isExpired() => 'Expiré',
                        default => 'Actif',
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Actif' => 'success',
                        'Expiré' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('login_url')
                    ->label('URL de connexion')
                    ->state(fn (Shop $record): ?string => $record->token
                        ? route('merchant.login', $record->token->uuid)
                        : null)
                    ->copyable()
                    ->placeholder('—'),
            ]);
    }

    private static function notes(): Section
    {
        return
            Section::make('Descriptions et note')
                ->icon(Heroicon::MusicalNote)
                ->columns(2)
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('comment1')
                        ->label('Commentaire 1')
                        ->prose(),
                    TextEntry::make('comment2')
                        ->label('Commentaire 2')
                        ->prose(),
                    TextEntry::make('comment3')
                        ->label('Commentaire 3')
                        ->prose(),
                    TextEntry::make('note')
                        ->label('Note')
                        ->prose(),
                ]);
    }
}
