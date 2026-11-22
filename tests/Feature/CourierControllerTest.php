<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_couriers_sorted_by_name_by_default(): void
    {
        Courier::query()->create([
            'code' => 'Z-CODE',
            'grouping' => 'EXPEDITION',
            'name' => 'Zebra Courier',
            'level' => 2,
        ]);
        Courier::query()->create([
            'code' => 'A-CODE',
            'grouping' => 'EXPEDITION',
            'name' => 'Alpha Courier',
            'level' => 3,
        ]);

        $response = $this->getJson('/api/courier?limit=1');

        $response->assertOk()
            ->assertJsonPath('per_page', 1)
            ->assertJsonPath('total', 2)
            ->assertJsonPath('data.0.name', 'Alpha Courier');
    }

    public function test_index_can_override_default_sort_by_created_at(): void
    {
        $older = Courier::query()->create([
            'code' => 'OLD',
            'grouping' => 'EXPEDITION',
            'name' => 'Older Courier',
            'level' => 1,
        ]);
        $newer = Courier::query()->create([
            'code' => 'NEW',
            'grouping' => 'EXPEDITION',
            'name' => 'Newer Courier',
            'level' => 1,
        ]);

        $older->forceFill(['created_at' => '2026-01-01 10:00:00'])->save();
        $newer->forceFill(['created_at' => '2026-02-01 10:00:00'])->save();

        $response = $this->getJson('/api/courier?order_by[created_at]=desc&limit=10');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Newer Courier');
    }

    public function test_index_search_matches_all_keywords_across_courier_name(): void
    {
        Courier::query()->create([
            'code' => 'BUDI',
            'grouping' => 'EXPEDITION',
            'name' => 'Budiono Hadi Agung',
            'level' => 2,
        ]);
        Courier::query()->create([
            'code' => 'OTHER',
            'grouping' => 'EXPEDITION',
            'name' => 'Courier Lain',
            'level' => 2,
        ]);

        $response = $this->getJson('/api/courier?search=budi+agung&limit=10');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Budiono Hadi Agung');
    }

    public function test_index_can_filter_multiple_levels(): void
    {
        Courier::query()->create([
            'code' => 'LV1',
            'grouping' => 'EXPEDITION',
            'name' => 'Level One',
            'level' => 1,
        ]);
        Courier::query()->create([
            'code' => 'LV2',
            'grouping' => 'EXPEDITION',
            'name' => 'Level Two',
            'level' => 2,
        ]);
        Courier::query()->create([
            'code' => 'LV3',
            'grouping' => 'EXPEDITION',
            'name' => 'Level Three',
            'level' => 3,
        ]);

        $response = $this->getJson('/api/courier?level=2,3&limit=10');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissing(['name' => 'Level One']);

        $levels = collect($response->json('data'))->pluck('level')->all();
        $this->assertEqualsCanonicalizing([2, 3], $levels);
    }

    public function test_store_creates_and_updates_courier(): void
    {
        $create = $this->postJson('/api/courier/store', [
            'code' => 'TEST',
            'grouping' => 'EXPEDITION',
            'name' => 'Test Courier',
            'level' => 2,
        ]);

        $create->assertStatus(201)
            ->assertJsonPath('code', 'TEST');

        $update = $this->postJson('/api/courier/store', [
            'id' => $create->json('id'),
            'code' => 'TEST-UPDATED',
            'grouping' => 'EXPEDITION',
            'name' => 'Updated Courier',
            'level' => 3,
        ]);

        $update->assertOk()
            ->assertJsonPath('code', 'TEST-UPDATED')
            ->assertJsonPath('name', 'Updated Courier');
    }

    public function test_store_validates_required_fields(): void
    {
        $this->postJson('/api/courier/store', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'grouping', 'name', 'level']);
    }

    public function test_destroy_soft_deletes_courier(): void
    {
        $courier = Courier::query()->create([
            'code' => 'DELETE',
            'grouping' => 'EXPEDITION',
            'name' => 'Delete Courier',
            'level' => 2,
        ]);

        $this->deleteJson('/api/courier/destroy/' . $courier->id)
            ->assertNoContent();

        $this->assertSoftDeleted('courier', ['id' => $courier->id]);
    }

    public function test_show_returns_all_couriers(): void
    {
        Courier::query()->create([
            'code' => 'ONE',
            'grouping' => 'EXPEDITION',
            'name' => 'One Courier',
            'level' => 1,
        ]);
        Courier::query()->create([
            'code' => 'TWO',
            'grouping' => 'EXPEDITION',
            'name' => 'Two Courier',
            'level' => 2,
        ]);

        $this->getJson('/api/courier/show')
            ->assertOk()
            ->assertJsonCount(2);
    }
}
