<?php

use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('division request conflicts are flagged and planning office can reschedule and approve', function () {
    $division = User::factory()->create([
        'email' => 'lingayen.admin@psu.local',
        'role' => 'admin',
    ]);
    $planningOffice = User::factory()->create([
        'email' => 'planning@psu.local',
        'role' => 'planning_office',
    ]);
    $start = now()->addDay()->setTime(10, 0);

    EventRequest::create([
        'name' => 'Existing event owner',
        'email' => 'owner@psu.local',
        'title' => 'Existing approved event',
        'venue_name' => 'Main Auditorium',
        'campus' => 'Lingayen Campus',
        'start_datetime' => $start,
        'end_datetime' => $start->copy()->addHour(),
        'status' => 'approved',
    ]);

    $this->actingAs($division)->post('/admin/request-venue', [
        'title' => 'Division seminar',
        'venue_name' => 'Main Auditorium',
        'campus' => 'Lingayen Campus',
        'sdg_number' => 4,
        'start_datetime' => $start->format('Y-m-d\\TH:i'),
        'end_datetime' => $start->copy()->addHour()->format('Y-m-d\\TH:i'),
        'description' => 'An education event',
    ])->assertRedirect();

    $request = EventRequest::where('title', 'Division seminar')->firstOrFail();
    expect($request->status)->toBe('conflict')
        ->and($request->sdg_number)->toBe(4);

    $this->actingAs($planningOffice)->get('/planning-office/pending-approvals')
        ->assertOk()
        ->assertSee('Conflict detected')
        ->assertSee('Existing approved event')
        ->assertSee('SDG 4: Quality Education');

    $newStart = $start->copy()->addHours(2);
    $this->actingAs($planningOffice)->post('/planning-office/approve/' . $request->id, [
        'venue_name' => 'Main Auditorium',
        'campus' => 'Lingayen Campus',
        'sdg_number' => 4,
        'start_datetime' => $newStart->format('Y-m-d\\TH:i'),
        'end_datetime' => $newStart->copy()->addHour()->format('Y-m-d\\TH:i'),
        'planning_note' => 'Moved to avoid the auditorium overlap.',
    ])->assertRedirect();

    $this->assertDatabaseHas('event_requests', [
        'id' => $request->id,
        'status' => 'approved',
        'sdg_number' => 4,
        'planning_note' => 'Moved to avoid the auditorium overlap.',
    ]);
});

test('public calendar lists only approved events and exposes no editing controls', function () {
    EventRequest::create([
        'name' => 'Division owner',
        'email' => 'division@psu.local',
        'title' => 'Public seminar',
        'venue_name' => 'Science Hall',
        'campus' => 'Alaminos Campus',
        'sdg_number' => 13,
        'start_datetime' => now()->addDay(),
        'end_datetime' => now()->addDay()->addHour(),
        'status' => 'approved',
    ]);
    EventRequest::create([
        'name' => 'Division owner',
        'email' => 'division@psu.local',
        'title' => 'Unpublished request',
        'venue_name' => 'Science Hall',
        'campus' => 'Alaminos Campus',
        'start_datetime' => now()->addDays(2),
        'end_datetime' => now()->addDays(2)->addHour(),
        'status' => 'pending',
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Public seminar')
        ->assertSee('SDG 13')
        ->assertDontSee('Unpublished request')
        ->assertDontSee('Submit Event Request');
});

test('requester offices cannot access planning office administration', function () {
    $requester = User::factory()->create([
        'email' => 'registrar@psu.local',
        'role' => 'office',
    ]);

    $this->actingAs($requester)
        ->get('/planning-office/manage-venues')
        ->assertRedirect('/planning-office/login');
});