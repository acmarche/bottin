<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

final class SitemapController
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), function (): string {
            return view('sitemap', [
                'shops' => Shop::query()->select(['slug', 'updated_at'])->orderBy('company')->get(),
                'categories' => Category::query()->select(['slug', 'updated_at'])->orderBy('name')->get(),
                'tags' => Tag::query()->where('private', false)->select(['slug', 'updated_at'])->orderBy('name')->get(),
            ])->render();
        });

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
