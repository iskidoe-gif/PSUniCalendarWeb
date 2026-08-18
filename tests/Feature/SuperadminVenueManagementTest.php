<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('superadmin can create a venue from the venue management page', function () {
    $user = User::factory()->create([
        'email' => 'superadmin-venues@example.com',
        'role' => 'superadmin',
    ]);

    $this->actingAs($user)
        ->get('/superadmin/manage-venues')
        ->assertOk()
        ->assertSee('Venue Management');

    $response = $this->actingAs($user)->post('/superadmin/manage-venues', [
        'name' => 'Laboratory Building',
    ]);

    $response->assertRedirect('/superadmin/manage-venues');
    $this->assertDatabaseHas('venues', ['name' => 'Laboratory Building']);
});

test('public calendar does not show request, venue, or logout controls', function () {
    $this->get('/')->assertOk()
        ->assertSee('Calendar')
        ->assertDontSee('View Venue Availability')
        ->assertDontSee('Venue Management')
        ->assertDontSee('Logout');
});
