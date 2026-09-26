<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('planning office can create a venue from the venue management page', function () {
    $user = User::factory()->create([
        'email' => 'planning-office-venues@example.com',
        'role' => 'planning_office',
    ]);

    $this->actingAs($user)
        ->get('/planning-office/manage-venues')
        ->assertOk()
        ->assertSee('Venue Management');

    $response = $this->actingAs($user)->post('/planning-office/manage-venues', [
        'name' => 'Laboratory Building',
    ]);

    $response->assertRedirect('/planning-office/manage-venues');
    $this->assertDatabaseHas('venues', ['name' => 'Laboratory Building']);
});

test('old superadmin entry points redirect to planning office', function () {
    $this->get('/superadmin/login')->assertRedirect('/planning-office/login');
    $this->get('/superadmin')->assertRedirect('/planning-office');
    $this->get('/superadmin/manage-venues')->assertRedirect('/planning-office/manage-venues');
});

test('public calendar does not show request, venue, or logout controls', function () {
    $this->get('/')->assertOk()
        ->assertSee('Calendar')
        ->assertDontSee('View Venue Availability')
        ->assertDontSee('Venue Management')
        ->assertDontSee('Logout');
});
