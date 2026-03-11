<?php

declare(strict_types=1);

namespace App\Filament\Merchant\Pages;

use App\Concerns\TracksHistoryTrait;
use App\Filament\Resources\Shops\Schemas\ShopForm;
use App\Models\Shop;
use App\Models\Token;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

final class EditShop extends Page
{
    use TracksHistoryTrait;

    /** @var array<string, mixed> */
    public ?array $data = [];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'Modifier';

    protected static ?string $title = 'Modifier ma fiche';

    protected static ?int $navigationSort = 2;

    /**
     * @var array<string, array<int>>
     */
    private array $oldRelationshipIds = [];

    public function mount(): void
    {
        $this->form->fill($this->getShop()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return ShopForm::forMerchant(
            $schema->statePath('data')->model($this->getShop()),
        );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save'),
            ]);
    }

    public function saveStep(): void
    {
        $this->getShop()->update($this->data);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->getShop()->update($data);

        Notification::make()
            ->title('Fiche mise à jour')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')
                ->label('Voir')
                ->icon(Heroicon::OutlinedEye)
                ->url(ViewShop::getUrl()),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $relationships = [
            'categories' => [
                'old' => $this->oldRelationshipIds['categories'],
                'new' => $record->categories()->pluck('categories.id')->toArray(),
                'label' => 'classement',
                'getDisplayName' => fn (int $id): string => $this->getCategoryName($id),
            ],
        ];

        $this->trackRelationships($record, $relationships);
    }

    private function getShop(): Shop
    {
        /** @var Token $token */
        $token = Auth::guard('merchant')->user();

        return $token->shop;
    }
}
