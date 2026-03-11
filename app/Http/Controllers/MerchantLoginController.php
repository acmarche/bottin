<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class MerchantLoginController
{
    public function __invoke(string $uuid): RedirectResponse
    {
        $token = Token::query()
            ->with('shop')
            ->where('uuid', $uuid)
            ->first();

        if ($token === null) {
            abort(404);
        }

        if ($token->isExpired()) {
            abort(403, 'Ce lien a expiré.');
        }

        Auth::guard('merchant')->login($token);

        return redirect()->to('/merchant');
    }
}
