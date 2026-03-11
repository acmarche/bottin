<?php

declare(strict_types=1);

use App\Http\Controllers\DownloadShopPdfController;
use App\Http\Controllers\MerchantLoginController;
use App\Livewire\Front\CategoryShow;
use App\Livewire\Front\HomePage;
use App\Livewire\Front\SearchResults;
use App\Livewire\Front\ShopDetail;
use App\Livewire\Front\ShopIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/annuaire', ShopIndex::class)->name('shops.index');
Route::get('/categorie/{category:slug}', CategoryShow::class)->name('category.show');
Route::get('/fiche/{shop:slug}', ShopDetail::class)->name('shop.show');
Route::get('/recherche', SearchResults::class)->name('search');

Route::get('/export-shop/{shop}', DownloadShopPdfController::class)->name('export.shop');

Route::get('/merchant/login/{uuid}', MerchantLoginController::class)->name('merchant.login');
// legacy backend url
Route::get('/backend/fiche/{uuid}', MerchantLoginController::class)->name('merchant.login');
