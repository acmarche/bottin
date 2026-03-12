<?php

declare(strict_types=1);

use App\Models\Token;

it('redirects legacy backend fiche url to merchant area', function (): void {
    $token = Token::factory()->create();

    $this->get('/backend/fiche/'.$token->uuid)
        ->assertRedirect('/merchant');
});

it('returns 404 for unknown uuid on legacy backend fiche url', function (): void {
    $this->get('/backend/fiche/ca316f97-7caf-49f1-8971-d912040239d9')
        ->assertNotFound();
});
