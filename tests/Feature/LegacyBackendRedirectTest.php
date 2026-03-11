<?php

declare(strict_types=1);

use App\Filament\Resources\Shops\ShopResource;
use App\Models\Token;

it('redirects legacy backend fiche url to filament shop view', function (): void {
    $token = Token::factory()->create();

    $this->get('/backend/fiche/'.$token->uuid)
        ->assertRedirect(ShopResource::getUrl('view', ['record' => $token->shop_id]));
});

it('returns 404 for unknown uuid on legacy backend fiche url', function (): void {
    $this->get('/backend/fiche/ca316f97-7caf-49f1-8971-d912040239d9')
        ->assertNotFound();
});
