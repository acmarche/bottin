<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Filament\Resources\Shops\Schemas\ScheduleForm;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Horaires';

    protected static ?string $label = 'Horaires';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return ScheduleForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day')
            ->columns([
                TextColumn::make('day')
                    ->label('Jour')
                    ->sortable()
                    ->formatStateUsing(fn (?int $state): string => match ($state) {
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
                TextColumn::make('morning_start')
                    ->label('Heure d\'ouverture (Matin)'),
                TextColumn::make('noon_start')
                    ->label('Heure d\'ouverture (Après-midi)'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
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
