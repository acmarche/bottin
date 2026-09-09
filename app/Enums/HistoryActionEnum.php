<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum HistoryActionEnum: string implements HasColor, HasLabel
{
    case Created = 'created';
    case Deleted = 'deleted';
    case Updated = 'updated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Created => 'Ajouté',
            self::Deleted => 'Supprimé',
            self::Updated => 'Modifié',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Created => 'success',
            self::Deleted => 'danger',
            self::Updated => 'gray',
        };
    }
}
