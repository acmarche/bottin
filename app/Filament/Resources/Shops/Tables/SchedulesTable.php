<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Tables;

use App\Filament\Resources\Shops\Schemas\ScheduleForm;
use App\Models\Schedule;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class SchedulesTable
{
    public static function configure(Table $table, int $countSchedule): Table
    {
        return $table
            ->defaultSort('day')
            ->recordTitleAttribute('day')
            ->columns([
                TextColumn::make('day')
                    ->label('Jour')
                    ->sortable()
                    ->formatStateUsing(fn(?int $state): string => match ($state) {
                        1 => 'Lundi',
                        2 => 'Mardi',
                        3 => 'Mercredi',
                        4 => 'Jeudi',
                        5 => 'Vendredi',
                        6 => 'Samedi',
                        7 => 'Dimanche',
                        default => '-',
                    }),
                IconColumn::make('is_closed')
                    ->label('Fermé')
                    ->boolean(),
                IconColumn::make('is_open_at_lunch')
                    ->label('Ouvert à midi')
                    ->boolean(),
                TextColumn::make('morning_start')
                    ->label('Matin')
                    ->state(function (Schedule $record): string {
                        return
                            $record->morning_start.' - '.$record->morning_end;
                    }),
                TextColumn::make('noon_start')
                    ->label('Après-midi')
                    ->state(function (Schedule $record): string {
                        return $record->noon_start.' - '.$record->noon_end;
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->hidden(fn(): bool => $countSchedule >= 7),
            ])
            ->recordActions([
                Action::make('copy')
                    ->label('Copier')
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([
                        Select::make('day')
                            ->label('Jour cible')
                            ->options(fn(Schedule $record): array => array_diff_key(
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
                ViewAction::make(),
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
