<?php

declare(strict_types=1);

use Illuminate\Contracts\Debug\ExceptionHandler;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

it('does not report tampered locked Livewire properties', function (): void {
    $handler = app(ExceptionHandler::class);

    expect($handler->shouldReport(new CannotUpdateLockedPropertyException('userUndertakingMultiFactorAuthentication')))
        ->toBeFalse()
        ->and($handler->shouldReport(new RuntimeException('boom')))
        ->toBeTrue();
});
