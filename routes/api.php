<?php

declare(strict_types=1);

use App\Http\Controllers\Api\LegacyBottinController;
use Illuminate\Support\Facades\Route;

Route::prefix('bottin')->group(function (): void {
    Route::get('commerces', [LegacyBottinController::class, 'commerces']);
    Route::get('fiches', [LegacyBottinController::class, 'fiches']);
    Route::get('fiches/category/{category}', [LegacyBottinController::class, 'fichesByCategory']);
    Route::get('fiche/{shop}', [LegacyBottinController::class, 'ficheById']);
    Route::get('fichebyslugname/{slug}', [LegacyBottinController::class, 'ficheBySlug']);
});
