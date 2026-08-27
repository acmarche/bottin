<?php

declare(strict_types=1);

namespace App\Input;

use App\Exceptions\PhoneFormattingUnavailableException;
use App\Support\PhoneFormatter;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

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
                if (blank($state)) {
                    return;
                }

                try {
                    $component->state(app(PhoneFormatter::class)->formatPhone($state));
                } catch (PhoneFormattingUnavailableException) {
                    Notification::make()
                        ->title('Formatage automatique indisponible')
                        ->body('Le numéro a été conservé tel quel, vérifiez son format.')
                        ->warning()
                        ->send();
                }
            });
    }
}
