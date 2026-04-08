<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\Schemas;

use App\Models\Schedule;
use App\Models\Shop;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

final class ScheduleForm
{
    public const array DAY_OPTIONS = [
        1 => 'Lundi',
        2 => 'Mardi',
        3 => 'Mercredi',
        4 => 'Jeudi',
        5 => 'Vendredi',
        6 => 'Samedi',
        7 => 'Dimanche',
    ];

    public static function configure(Schema $schema, null|Model|Shop $owner = null): Schema
    {
        return $schema
            ->components([
                Select::make('day')
                    ->label('Jour')
                    ->options(function (?Schedule $record) use ($schema): array {
                        $livewire = $schema->getLivewire();
                        if (!$livewire instanceof RelationManager) {
                            return self::DAY_OPTIONS;
                        }

                        $shopId = $livewire->getOwnerRecord()->getKey();

                        $usedDays = Schedule::query()
                            ->where('shop_id', $shopId)
                            ->when($record, fn($query) => $query->where('id', '!=', $record->getKey()))
                            ->pluck('day')
                            ->all();

                        return array_diff_key(self::DAY_OPTIONS, array_flip($usedDays));
                    })
                    ->required()
                    ->unique(table: Schedule::class, column: 'day', modifyRuleUsing: function ($rule) use ($schema) {
                        $livewire = $schema->getLivewire();
                        if (!$livewire instanceof RelationManager) {
                            return $rule;
                        }

                        $shopId = $livewire->getOwnerRecord()->getKey();

                        return $rule->where('shop_id', $shopId);
                    }, ignoreRecord: true),
                Toggle::make('is_closed')
                    ->label('Fermé')
                    ->default(false),
                Toggle::make('is_open_at_lunch')
                    ->label('Ouvert à midi')
                    ->default(false),
                Toggle::make('is_by_appointment')
                    ->label('Sur rendez-vous')
                    ->default(false),
                TimePicker::make('morning_start')
                    ->label('Heure d\'ouverture')
                    ->suffix('matin')
                    ->seconds(false),
                TimePicker::make('morning_end')
                    ->label('Heure de fermeture')
                    ->suffix('matin')
                    ->seconds(false),
                TimePicker::make('noon_start')
                    ->label('Heure d\'ouverture')
                    ->suffix('après-midi')
                    ->seconds(false),
                TimePicker::make('noon_end')
                    ->label('Heure de fermeture')
                    ->suffix('après-midi')
                    ->seconds(false),
            ]);
    }
}
