<?php

declare(strict_types=1);

use App\Exceptions\PhoneFormattingUnavailableException;
use App\Support\PhoneFormatter;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;
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

it('throws and logs a warning when the AI request fails', function (): void {
    OpenAI::fake([
        new ErrorException([
            'message' => 'Incorrect API key provided.',
            'type' => 'invalid_request_error',
            'code' => 'invalid_api_key',
        ], new Response(401)),
    ]);

    Log::spy();

    expect(fn (): string => (new PhoneFormatter)->formatPhone('084/22.44.33'))
        ->toThrow(PhoneFormattingUnavailableException::class);

    Log::shouldHaveReceived('warning')->once();
});
