<?php

declare(strict_types=1);

use App\Models\Category;

it('generates a slug on creation', function (): void {
    $category = Category::factory()->create(['name' => 'My Category']);

    expect($category->slug)->toBe('my-category');
});

it('updates slug when name changes', function (): void {
    $category = Category::factory()->create(['name' => 'Original']);

    $category->update(['name' => 'Updated Name']);

    expect($category->fresh()->slug)->toBe('updated-name');
});

it('generates unique slugs', function (): void {
    $cat1 = Category::factory()->create(['name' => 'Duplicate']);
    $cat2 = Category::factory()->create(['name' => 'Duplicate']);

    expect($cat1->slug)->toBe('duplicate');
    expect($cat2->slug)->toBe('duplicate-1');
});

it('handles empty source field', function (): void {
    $category = Category::factory()->create(['name' => '']);

    expect($category->slug)->toBe('n-a');
});
