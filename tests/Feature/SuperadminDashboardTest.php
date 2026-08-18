<?php

use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('superadmin dashboard renders for authenticated superadmins', function () {
    $user = User::factory()->create([
        'email' => 'superadmin@example.com',
        'role' => 'superadmin',
    ]);

    $response = $this->actingAs($user)->get('/superadmin');

    $response->assertStatus(200);
    $response->assertSee('System Overview');
});

test('superadmin dashboard keeps the live master calendar visible', function () {
    $user = User::factory()->create([
        'email' => 'superadmin-no-calendar@example.com',
        'role' => 'superadmin',
    ]);

    $response = $this->actingAs($user)->get('/superadmin');

    $response->assertStatus(200);
    $response->assertSee('Master Live Calendar');
    $this->get('/superadmin/calendar')->assertNotFound();
});

test('superadmin dashboard shows campus event totals and upcoming events summary', function () {
    $user = User::factory()->create([
        'email' => 'superadmin-summary@example.com',
        'role' => 'superadmin',
    ]);

    EventRequest::create([
        'name' => 'Admin One',
        'email' => 'admin1@psu.local',
        'title' => 'Alaminos Event',
        'venue_name' => 'Alaminos Hall',
        'campus' => 'Alaminos Campus',
        'description' => 'Alaminos sample',
        'start_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    EventRequest::create([
        'name' => 'Admin Two',
        'email' => 'admin2@psu.local',
        'title' => 'Lingayen Event',
        'venue_name' => 'Lingayen Hall',
        'campus' => 'Lingayen Campus',
        'description' => 'Lingayen sample',
        'start_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDays(2)->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    EventRequest::create([
        'name' => 'Admin Three',
        'email' => 'admin3@psu.local',
        'title' => 'University Event',
        'venue_name' => 'University Plaza',
        'campus' => 'All Campus',
        'description' => 'University-wide sample',
        'start_datetime' => now()->addDays(3)->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDays(3)->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    EventRequest::create([
        'name' => 'Admin Four',
        'email' => 'admin4@psu.local',
        'title' => 'Past Event',
        'venue_name' => 'Binmaley Hall',
        'campus' => 'Binmaley Campus',
        'description' => 'Past sample',
        'start_datetime' => now()->subDays(2)->format('Y-m-d H:i:s'),
        'end_datetime' => now()->subDays(2)->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    $response = $this->actingAs($user)->get('/superadmin');

    $response->assertStatus(200);
    $response->assertSee('Alaminos Campus');
    $response->assertSee('Lingayen Campus');
    $response->assertSee('Binmaley Campus');
    $response->assertSee('University-wide events');
    $response->assertSee('Upcoming events');
    $response->assertSee('Campus event trends');
    $response->assertSee('1', false);
});

test('superadmin can review pending requests with campus metadata', function () {
    $user = User::factory()->create([
        'email' => 'superadmin-review@example.com',
        'role' => 'superadmin',
    ]);

    EventRequest::create([
        'name' => 'Admin User',
        'email' => 'admin@psu.local',
        'title' => 'Campus Event',
        'venue_name' => 'Alaminos Hall',
        'campus' => 'Alaminos Campus',
        'description' => 'Sample request',
        'start_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->get('/superadmin/pending-approvals');

    $response->assertStatus(200);
    $response->assertSee('Pending Approvals');
    $response->assertSee('Alaminos Campus');
    $response->assertSee('Approve');
    $response->assertSee('Reject');
});

test('admin dashboard totals are scoped to the admin campus plus university-wide events', function () {
    $user = User::factory()->create([
        'email' => 'alaminos.admin@psu.local',
        'role' => 'admin',
    ]);

    EventRequest::create([
        'name' => 'Alaminos Admin',
        'email' => 'alaminos.admin@psu.local',
        'title' => 'Alaminos Event',
        'venue_name' => 'Alaminos Hall',
        'campus' => 'Alaminos Campus',
        'description' => 'Local event',
        'start_datetime' => now()->addDay()->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    EventRequest::create([
        'name' => 'University Admin',
        'email' => 'general.admin@psu.local',
        'title' => 'University Event',
        'venue_name' => 'University Plaza',
        'campus' => 'All Campus',
        'description' => 'University-wide event',
        'start_datetime' => now()->addDays(2)->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDays(2)->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    EventRequest::create([
        'name' => 'Lingayen Admin',
        'email' => 'lingayen.admin@psu.local',
        'title' => 'Lingayen Event',
        'venue_name' => 'Lingayen Hall',
        'campus' => 'Lingayen Campus',
        'description' => 'Other campus event',
        'start_datetime' => now()->addDays(3)->format('Y-m-d H:i:s'),
        'end_datetime' => now()->addDays(3)->addHour()->format('Y-m-d H:i:s'),
        'status' => 'approved',
    ]);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertOk()
        ->assertViewHas('totalEvents', 2)
        ->assertViewHas('upcomingEvents', 2);
});

test('admin request submission stores the campus from which the request came', function () {
    $user = User::factory()->create([
        'email' => 'admin@psu.local',
        'role' => 'admin',
    ]);

    $response = $this->actingAs($user)->post('/admin/request-venue', [
        'title' => 'Campus Meeting',
        'venue_name' => 'Lingayen Hall',
        'campus' => 'Lingayen Campus',
        'start_datetime' => now()->addDay()->format('Y-m-d\TH:i'),
        'end_datetime' => now()->addDay()->addHour()->format('Y-m-d\TH:i'),
        'description' => 'Request from Lingayen',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('event_requests', [
        'email' => 'admin@psu.local',
        'campus' => 'Lingayen Campus',
        'status' => 'pending',
    ]);
});
