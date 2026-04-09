<?php

declare(strict_types=1);

namespace App\Filament\Resources\Shops\RelationManagers;

use App\Filament\Resources\Shops\Schemas\ScheduleForm;
use App\Filament\Resources\Shops\Tables\SchedulesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

final class SchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'schedules';

    protected static ?string $title = 'Horaires détaillés';

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
        return SchedulesTable::configure($table, $this->getOwnerRecord()->schedules()->count());
    }
}
