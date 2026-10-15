<?php

namespace Tests\Feature\Admin;

use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\IAM\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->user->assignRole($role);
});

it('can fetch all thuong hieus', function () {
    ThuongHieu::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->getJson('/api/admin/thuong-hieu');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonCount(3, 'data');
});

it('can create a thuong hieu', function () {
    $data = [
        'code' => 'TH001',
        'name' => 'Yamaha',
        'logo_path' => 'yamaha.png',
        'status' => true,
    ];

    $response = $this->actingAs($this->user)->postJson('/api/admin/thuong-hieu', $data);

    $response->assertStatus(201)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonPath('data.name', 'Yamaha');

    $this->assertDatabaseHas('thuong_hieus', [
        'name' => 'Yamaha',
    ]);
});

it('can update a thuong hieu', function () {
    $brand = ThuongHieu::factory()->create([
        'name' => 'Old Name',
    ]);

    $data = [
        'code' => $brand->code,
        'name' => 'New Name',
        'logo_path' => 'new.png',
        'status' => true,
    ];

    $response = $this->actingAs($this->user)->putJson("/api/admin/thuong-hieu/{$brand->id}", $data);

    $response->assertStatus(200)
             ->assertJsonPath('data.name', 'New Name');

    $this->assertDatabaseHas('thuong_hieus', [
        'id' => $brand->id,
        'name' => 'New Name',
    ]);
});

it('can delete a thuong hieu', function () {
    $brand = ThuongHieu::factory()->create();

    $response = $this->actingAs($this->user)->deleteJson("/api/admin/thuong-hieu/{$brand->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('thuong_hieus', [
        'id' => $brand->id,
    ]);
});
