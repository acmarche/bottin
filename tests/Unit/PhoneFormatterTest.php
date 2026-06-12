<?php

declare(strict_types=1);

use App\Support\PhoneFormatter;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

it('returns an empty string untouched without calling the AI', function (): void {
    OpenAI::fake();

    expect((new PhoneFormatter)->formatPhone(''))->toBe('');

    OpenAI::assertNothingSent();
});

it('keeps an already well formatted number without calling the AI', function (): void {
    OpenAI::fake();

    expect((new PhoneFormatter)->formatPhone('+32 84 22 44 33'))->toBe('+32 84 22 44 33');

    OpenAI::assertNothingSent();
});

it('formats a badly formatted number using the AI', function (): void {
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                ['message' => ['content' => '{"phone": "+32 84 22 44 33"}']],
            ],
        ]),
    ]);

    expect((new PhoneFormatter)->formatPhone('084/22.44.33'))->toBe('+32 84 22 44 33');
});

it('falls back to the original value when the AI cannot recover it', function (): void {
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                ['message' => ['content' => '{"phone": null}']],
            ],
        ]),
    ]);

    expect((new PhoneFormatter)->formatPhone('not a phone'))->toBe('not a phone');
});
