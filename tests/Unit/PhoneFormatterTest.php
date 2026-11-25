<?php

declare(strict_types=1);

use App\Exceptions\PhoneFormattingUnavailableException;
use App\Support\PhoneFormatter;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Resources\Chat;
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

it('keeps an already well formatted number with a one digit area code without calling the AI', function (string $phone): void {
    OpenAI::fake();

    expect((new PhoneFormatter)->formatPhone($phone))->toBe($phone);

    OpenAI::assertNothingSent();
})->with([
    'brussels' => '+32 2 682 19 91',
    'mobile' => '+32 475 12 34 56',
]);

it('formats a brussels number using the AI', function (): void {
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [
                ['message' => ['content' => '{"phone": "+32 2 682 19 91"}']],
            ],
        ]),
    ]);

    expect((new PhoneFormatter)->formatPhone('02/682.19.91'))->toBe('+32 2 682 19 91');

    OpenAI::assertSent(Chat::class, fn (string $method, array $parameters): bool => str_contains($parameters['messages'][0]['content'], '+32 2 682 19 91'));
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
