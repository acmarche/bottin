<?php

declare(strict_types=1);

use Filament\Notifications\Livewire\Notifications;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

use function Pest\Livewire\livewire;

it('does not report tampered locked Livewire properties', function (): void {
    $handler = app(ExceptionHandler::class);

    expect($handler->shouldReport(new CannotUpdateLockedPropertyException('userUndertakingMultiFactorAuthentication')))
        ->toBeFalse()
        ->and($handler->shouldReport(new RuntimeException('boom')))
        ->toBeTrue();
});

it('does not report type errors from tampered Livewire update payloads', function (): void {
    $handler = app(ExceptionHandler::class);

    try {
        livewire(Notifications::class)->set('notifications', [1]);
        $this->fail('Expected a TypeError from the tampered payload.');
    } catch (TypeError $typeError) {
        expect($handler->shouldReport($typeError))->toBeFalse();
    }

    expect($handler->shouldReport(new TypeError('boom')))->toBeTrue();
});
