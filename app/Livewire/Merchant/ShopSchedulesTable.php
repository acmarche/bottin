<?php

declare(strict_types=1);

namespace App\Livewire\Merchant;

use App\Filament\Resources\Shops\Schemas\ScheduleForm;
use App\Models\Schedule;
use App\Models\Shop;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;

final class ShopSchedulesTable extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public int $shopId;

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): HasMany => $this->getShop()->schedules())
            ->defaultSort('day')
            ->recordTitleAttribute('day')
            ->columns([
                TextColumn::make('day')
                    ->label('Jour')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state): string => ScheduleForm::DAY_OPTIONS[$state] ?? '-'),
                IconColumn::make('is_closed')
                    ->label('Fermé')
                    ->boolean()
                    ->falseIcon(false),
                IconColumn::make('is_by_appointment')
                    ->label('Sur rendez-vous')
                    ->boolean()
                    ->falseIcon(false),
                TextColumn::make('morning_start')
                    ->label('Matin')
                    ->state(fn (Schedule $record): string => mb_substr((string) $record->morning_start, 0, 5).' - '.mb_substr((string) $record->morning_end, 0, 5)),
                TextColumn::make('noon_start')
                    ->label('Après-midi')
                    ->state(fn (Schedule $record): string => mb_substr((string) $record->noon_start, 0, 5).' - '.mb_substr((string) $record->noon_end, 0, 5)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Ajouter un horaire')
                    ->schema(fn (Schema $schema): Schema => ScheduleForm::configure($schema))
                    ->hidden(fn (): bool => $this->getShop()->schedules()->count() >= 7),
            ])
            ->recordActions([
                Action::make('copy')
                    ->label('Copier')
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([
                        Select::make('day')
                            ->label('Jour cible')
                            ->options(fn (Schedule $record): array => array_diff_key(
                                ScheduleForm::DAY_OPTIONS,
                                [$record->day => true],
                            ))
                            ->required(),
                    ])
                    ->action(function (Schedule $record, array $data): void {
                        Schedule::query()
                            ->where('shop_id', $record->shop_id)
                            ->where('day', $data['day'])
                            ->delete();

                        $record->replicate(['id'])
                            ->fill(['day' => $data['day']])
                            ->save();
                    }),
                EditAction::make()
                    ->schema(fn (Schema $schema): Schema => ScheduleForm::configure($schema)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.merchant.table');
    }

    private function getShop(): Shop
    {
        return Shop::findOrFail($this->shopId);
    }
}
