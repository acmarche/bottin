<?php

declare(strict_types=1);

namespace App\Input;

use Filament\Forms\Components\TextInput;

final class PhoneInput
{
    public static function create(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->tel()
            ->telRegex('/^\+\d{1,3}(\s\d{2,4}){2,4}$/')
            ->maxLength(120)
            ->validationMessages([
                'regex' => 'Le numéro doit être au format international (ex: +32 84 22 44 33).',
            ]);
    }
}
