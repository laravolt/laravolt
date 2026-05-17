<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Item\Models\Item;
use Tests\TestCase;

uses(TestCase::class, LazilyRefreshDatabase::class);

it('renders the thunderclap-generated index without app-local table partials', function () {
    $user = User::factory()->create();
    Item::factory()->count(3)->create();

    $html = $this->actingAs($user)
        ->get(route('modules::item.index'))
        ->assertOk()
        ->getContent();

    file_put_contents('/tmp/thunderclap-rendered-index.html', $html);

    // Generated index uses the unified Volt UI primitives (TableView /
    // Suitable, Livewire-mounted), not bespoke app-local table partials.
    expect($html)
        ->toContain('wire:id')                                  // Livewire mount marker
        ->toContain('modules.item.item-table-view')             // Livewire component name
        ->toContain('data-role="suitable"')                     // unified TableView root
        ->toContain('/modules/item');                           // route("modules::item.index")
});
