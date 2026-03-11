<?php

declare(strict_types=1);

use App\Models\Shop;
use App\Models\Token;

use function Pest\Laravel\artisan;

it('regenerates tokens for all shops', function () {
    $shops = Shop::factory()->count(3)->create();

    artisan('bottin:token', ['action' => 'regenerate'])
        ->assertSuccessful()
        ->expectsOutputToContain('3 token(s) generated.');

    expect(Token::count())->toBe(3);
});

it('updates existing tokens when regenerating', function () {
    $shop = Shop::factory()->create();
    $existingToken = Token::factory()->create(['shop_id' => $shop->id]);

    artisan('bottin:token', ['action' => 'regenerate'])
        ->assertSuccessful();

    expect(Token::count())->toBe(1);

    $updatedToken = Token::first();
    expect($updatedToken->uuid)->not->toBe($existingToken->uuid)
        ->and($updatedToken->shop_id)->toBe($shop->id);
});

it('fails when no shops without tokens exist for generate', function () {
    $shop = Shop::factory()->create();
    Token::factory()->create(['shop_id' => $shop->id]);

    artisan('bottin:token', ['action' => 'generate'])
        ->assertFailed()
        ->expectsOutputToContain('No shops without tokens found.');
});

it('fails with invalid action', function () {
    artisan('bottin:token', ['action' => 'invalid'])
        ->assertFailed()
        ->expectsOutputToContain('Invalid action');
});

it('generates a token with correct attributes', function () {
    Shop::factory()->count(2)->create();

    artisan('bottin:token', ['action' => 'regenerate'])
        ->assertSuccessful();

    $token = Token::first();

    expect($token->uuid)->toBeString()
        ->and(mb_strlen($token->password))->toBe(50)
        ->and($token->expire_at->isFuture())->toBeTrue()
        ->and($token->shop_id)->not->toBeNull();
});
