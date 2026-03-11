<?php

declare(strict_types=1);

use App\Models\Token;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\get;

beforeEach(function () {
    Auth::logout();
});

it('can login with a valid token uuid', function () {
    $token = Token::factory()->create();

    get(route('merchant.login', $token->uuid))
        ->assertRedirect('/merchant');

    expect(Auth::guard('merchant')->check())->toBeTrue()
        ->and(Auth::guard('merchant')->id())->toBe($token->id);
});

it('returns 404 for an invalid token uuid', function () {
    get(route('merchant.login', 'non-existent-uuid'))
        ->assertNotFound();
});

it('returns 403 for an expired token', function () {
    $token = Token::factory()->create([
        'expire_at' => now()->subDay(),
    ]);

    get(route('merchant.login', $token->uuid))
        ->assertForbidden();
});

it('can access the merchant edit shop page when authenticated', function () {
    $token = Token::factory()->create();

    Auth::guard('merchant')->login($token);

    $this->actingAs($token, 'merchant')
        ->get('/merchant/edit-shop')
        ->assertOk();
});

it('cannot access the merchant panel without authentication', function () {
    get('/merchant/edit-shop')
        ->assertRedirect();
});
