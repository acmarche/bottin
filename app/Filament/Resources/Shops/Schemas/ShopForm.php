<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Repository\ShopRepository;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Livewire\Component;

final class ShopForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        self::generalTab(),
                        self::contactTab(),
                        self::contactPersonTab(),
                        self::socialTab(),
                        // self::featuresTab(),
                        self::tagsTab(),
                        self::notesTab(),
                        self::mapTab(),
                        self::adminContactTab(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function forMerchant(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    self::generalStep()
                        ->afterValidation(fn(Component $livewire) => $livewire->saveStep()),
                    self::socialStep()
                        ->afterValidation(function () {
                            $data = $this->form->getState();
                            $this->record->update($data);
                        }),
                    self::notesStep()
                        ->afterValidation(function () {
                            $data = $this->form->getState();
                            $this->record->update($data);
                        }),
                    self::mapStep(),
                ])
                    ->columnSpanFull()
                    ->skippable()
                    ->persistStepInQueryString('step')
                    ->submitAction(
                        new HtmlString(
                            '<x-filament::button type="submit" wire:click="save">Enregistrer</x-filament::button>'
                        )
                    ),
            ]);
    }

    public static function toCreate(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('company')
                ->label('Nom de la société')
                ->placeholder('Rechercher pour une société existante...')
                ->live(debounce: 500)
                ->afterStateUpdated(fn(?string $state) => ShopRepository::searchByName($state ?? ''))
                ->autocomplete(false)
                ->autofocus(),
        ]);
    }

    public static function fieldsReminder(): array
    {
        return
            [
                TagsInput::make('recipients')
                    ->label('Destinataires')
                    ->placeholder('Ajouter un email')
                    ->required(),
                TextInput::make('subject')
                    ->label('Sujet')
                    ->required(),
                Textarea::make('content')
                    ->label('Contenu')
                    ->required(),
            ];
    }

    private static function generalTab(): Tab
    {
        return Tab::make('Général')
            ->icon(Heroicon::InformationCircle)
            ->columns(2)
            ->schema(ShopFormColumns::generalColumns());
    }

    private static function mapTab(): Tab
    {
        return Tab::make('Carte')
            ->columns(2)
            ->icon(Heroicon::MapPin)
            ->schema(ShopFormColumns::mapColumns());
    }

    private static function contactTab(): Tab
    {
        return Tab::make('Contact')
            ->icon(Heroicon::Phone)
            ->columns(2)
            ->schema(ShopFormColumns::contactColumns());
    }

    private static function socialTab(): Tab
    {
        return Tab::make('Réseaux sociaux')
            ->icon(Heroicon::GlobeAlt)
            ->columns(2)
            ->schema(ShopFormColumns::socialColumns());
    }

    private static function contactPersonTab(): Tab
    {
        return Tab::make('Autre contact')
            ->icon(Heroicon::UserCircle)
            ->columns(2)
            ->schema(ShopFormColumns::contactPersonColumns());
    }

    private static function adminContactTab(): Tab
    {
        return Tab::make('Contact administratif')
            ->icon(Heroicon::ShieldCheck)
            ->columns(2)
            ->schema(ShopFormColumns::adminColumns());
    }

    private static function tagsTab(): Tab
    {
        return Tab::make('Tags')
            ->icon(Heroicon::Tag)
            ->schema(ShopFormColumns::tagColumns());
    }

    private static function notesTab(): Tab
    {
        return Tab::make('Descriptions et note')
            ->icon(Heroicon::ChatBubbleBottomCenterText)
            ->schema(ShopFormColumns::notesColumns());
    }

    private static function generalStep(): Step
    {
        return Step::make('Général')
            ->icon(Heroicon::InformationCircle)
            ->description('Informations de base')
            ->columns(2)
            ->schema([...ShopFormColumns::generalColumns(), ...ShopFormColumns::contactColumns()]);
    }

    private static function socialStep(): Step
    {
        return Step::make('Réseaux sociaux')
            ->icon(Heroicon::GlobeAlt)
            ->description('Présence en ligne')
            ->columns(2)
            ->schema(ShopFormColumns::socialColumns());
    }

    private static function notesStep(): Step
    {
        return Step::make('Descriptions')
            ->icon(Heroicon::ChatBubbleBottomCenterText)
            ->description('Commentaires et descriptions')
            ->schema(ShopFormColumns::notesColumns());
    }

    private static function mapStep(): Step
    {
        return Step::make('Carte')
            ->icon(Heroicon::MapPin)
            ->description('Localisation sur la carte')
            ->columns(2)
            ->schema(ShopFormColumns::mapColumns());
    }
}
