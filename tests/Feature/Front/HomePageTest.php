<?php

declare(strict_types=1);

use App\Livewire\Front\HomePage;
use App\Models\Category;
use Livewire\Livewire;

it('renders the home page', function (): void {
    $this->get(route('home'))->assertSuccessful();
});

it('displays root categories', function (): void {
    $root = Category::factory()->create(['name' => 'Alimentation']);

    Livewire::test(HomePage::class)
        ->assertSee('Alimentation');
});

it('displays children of root categories', function (): void {
    $root = Category::factory()->create(['name' => 'Services']);
    $child = Category::factory()->create(['name' => 'Banques', 'parent_id' => $root->id]);

    Livewire::test(HomePage::class)
        ->assertSee('Services')
        ->assertSee('Banques');
});

it('does not display non-root categories as cards', function (): void {
    $root = Category::factory()->create(['name' => 'Root']);
    $child = Category::factory()->create(['name' => 'Child', 'parent_id' => $root->id]);
    $grandchild = Category::factory()->create(['name' => 'Grandchild', 'parent_id' => $child->id]);

    $response = $this->get(route('home'));
    $response->assertSuccessful();
    $response->assertSee('Root');
    $response->assertSee('Child');
    $response->assertDontSee('Grandchild');
});
