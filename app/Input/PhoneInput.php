<?php

declare(strict_types=1);

namespace App\Input;

use App\Support\PhoneFormatter;
use Filament\Forms\Components\TextInput;

final class PhoneInput
{
    public static function create(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->tel()
            ->maxLength(120)
            ->live(onBlur: true)
            ->afterStateUpdated(function (TextInput $component, ?string $state): void {
                if (filled($state)) {
                    $component->state(app(PhoneFormatter::class)->formatPhone($state));
                }
            });
    }
}
