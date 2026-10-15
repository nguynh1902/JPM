<?php

namespace Tests\Feature\Admin;

use App\Domain\CMS\Models\Faq;
use App\Domain\IAM\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->user->assignRole($role);
});

it('can fetch all faqs', function () {
    Faq::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->getJson('/api/admin/faq');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonCount(3, 'data');
});

it('can create a faq', function () {
    $data = [
        'question' => 'How to reset password?',
        'answer' => 'Click on the forget password link.',
        'category' => 'Account',
        'status' => 'ACTIVE',
    ];

    $response = $this->actingAs($this->user)->postJson('/api/admin/faq', $data);

    $response->assertStatus(201)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonPath('data.question', 'How to reset password?');

    $this->assertDatabaseHas('faqs', [
        'question' => 'How to reset password?',
    ]);
});

it('can update a faq', function () {
    $faq = Faq::factory()->create([
        'question' => 'Old Question',
    ]);

    $data = [
        'question' => 'New Question',
        'answer' => 'New Answer',
        'category' => 'Account',
        'status' => 'ACTIVE',
    ];

    $response = $this->actingAs($this->user)->putJson("/api/admin/faq/{$faq->id}", $data);

    $response->assertStatus(200)
             ->assertJsonPath('data.question', 'New Question');

    $this->assertDatabaseHas('faqs', [
        'id' => $faq->id,
        'question' => 'New Question',
    ]);
});

it('can delete a faq', function () {
    $faq = Faq::factory()->create();

    $response = $this->actingAs($this->user)->deleteJson("/api/admin/faq/{$faq->id}");

    $response->assertStatus(200);

    $this->assertDatabaseMissing('faqs', [
        'id' => $faq->id,
    ]);
});
