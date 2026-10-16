<?php

use App\Models\ActivityLog;
use App\Models\EventRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function dashboardEvent(array $attributes = []): EventRequest
{
    $start = $attributes['start_datetime'] ?? now()->addDays(3)->setTime(9, 0);

    return EventRequest::create(array_merge([
        'name' => 'Office of Student Affairs',
        'email' => 'osa@psu.local',
        'title' => 'Sample event',
        'venue_name' => 'Main Hall',
        'campus' => 'Lingayen Campus',
        'start_datetime' => $start,
        'end_datetime' => $start->copy()->addHours(2),
        'status' => 'approved',
    ], $attributes));
}

beforeEach(function () {
    $this->planning = User::factory()->create(['role' => 'planning_office', 'email' => 'planning@psu.local']);
});

test('dashboard shows real counts and counts university-wide events once', function () {
    dashboardEvent(['title' => 'Lingayen Intrams']);
    dashboardEvent(['title' => 'University Meet', 'is_university_wide' => true, 'venue_name' => 'Plaza']);
    dashboardEvent(['title' => 'Finished Seminar', 'start_datetime' => now()->subDays(5)->setTime(9, 0)]);
    dashboardEvent(['title' => 'Alaminos Request', 'campus' => 'Alaminos Campus', 'venue_name' => 'Alaminos Gym', 'status' => 'pending']);

    $response = $this->actingAs($this->planning)->get('/planning-office')->assertOk()
        ->assertSee('Planning Office Dashboard')
        ->assertSee('Alaminos Request')
        ->assertSee('Campus-specific');

    $summary = $response->viewData('summary');
    expect($summary['total'])->toBe(3)
        ->and($summary['upcoming'])->toBe(2)
        ->and($summary['completed'])->toBe(1)
        ->and($summary['pending'])->toBe(1);

    $byCampus = array_combine($response->viewData('charts')['byCampus']['labels'], $response->viewData('charts')['byCampus']['values']);
    expect($byCampus['Lingayen Campus'])->toBe(2)
        ->and($byCampus['University-wide events'])->toBe(1)
        ->and(array_sum($byCampus))->toBe(3);
});

test('campus filter keeps that campus plus university-wide events', function () {
    dashboardEvent(['title' => 'Lingayen Intrams']);
    dashboardEvent(['title' => 'Binmaley Fair', 'campus' => 'Binmaley Campus', 'venue_name' => 'Binmaley Hall']);
    dashboardEvent(['title' => 'University Meet', 'is_university_wide' => true, 'venue_name' => 'Plaza']);

    $response = $this->actingAs($this->planning)->get('/planning-office?campus=Binmaley+Campus')->assertOk();

    expect($response->viewData('summary')['total'])->toBe(2)
        ->and($response->viewData('upcoming')->pluck('title')->all())->toEqualCanonicalizing(['Binmaley Fair', 'University Meet']);
});

test('invalid dashboard filters are rejected', function () {
    $this->actingAs($this->planning)->get('/planning-office?campus=Fake+Campus')->assertSessionHasErrors('campus');
    $this->actingAs($this->planning)->get('/planning-office?from=2026-10-10&to=2026-10-01')->assertSessionHasErrors('to');
});

test('conflicting submissions are listed as scheduling conflicts', function () {
    dashboardEvent(['title' => 'Approved Booking']);
    dashboardEvent(['title' => 'Clashing Request', 'status' => 'conflict']);

    $response = $this->actingAs($this->planning)->get('/planning-office')->assertOk();

    expect($response->viewData('summary')['conflicts'])->toBe(1)
        ->and($response->viewData('conflicts')->first()['type'])->toBe('Overlaps an approved event');
});

test('planning office can return a request for revision and it is logged', function () {
    $request = dashboardEvent(['status' => 'pending', 'title' => 'Needs Changes']);

    $this->actingAs($this->planning)->post("/planning-office/return/{$request->id}", ['planning_note' => ''])
        ->assertSessionHasErrors('planning_note');

    $this->actingAs($this->planning)->post("/planning-office/return/{$request->id}", ['planning_note' => 'Please attach the program.'])
        ->assertSessionHas('success');

    $request->refresh();
    expect($request->status)->toBe('returned')
        ->and($request->planning_note)->toBe('Please attach the program.')
        ->and(ActivityLog::where('action', 'returned')->where('email', 'planning@psu.local')->exists())->toBeTrue();
});

test('approving a conflict request records a conflict resolution', function () {
    $request = dashboardEvent(['status' => 'conflict', 'title' => 'Was Clashing']);

    $this->actingAs($this->planning)->post("/planning-office/approve/{$request->id}")->assertRedirect();

    expect($request->refresh()->status)->toBe('approved')
        ->and(ActivityLog::where('action', 'conflict_resolved')->exists())->toBeTrue();
});

test('only the planning office can return requests', function () {
    $request = dashboardEvent(['status' => 'pending']);
    $office = User::factory()->create(['role' => 'office']);

    $this->actingAs($office)->post("/planning-office/return/{$request->id}", ['planning_note' => 'x'])
        ->assertRedirect('/planning-office/login');
    expect($request->refresh()->status)->toBe('pending');
});

test('the planning office calendar shows approved events only', function () {
    dashboardEvent(['title' => 'Approved On Calendar']);
    dashboardEvent(['title' => 'Pending Off Calendar', 'status' => 'pending']);
    dashboardEvent(['title' => 'Conflict Off Calendar', 'status' => 'conflict']);

    $titles = $this->actingAs($this->planning)->get('/planning-office')->assertOk()
        ->viewData('calendarEvents')->pluck('title')->all();

    expect($titles)->toBe(['Approved On Calendar']);
});

test('pending and returned requests never appear on the public calendar', function () {
    dashboardEvent(['title' => 'Public Approved Event']);
    dashboardEvent(['title' => 'Secret Pending Event', 'status' => 'pending']);
    dashboardEvent(['title' => 'Secret Returned Event', 'status' => 'returned']);

    $this->get('/')->assertOk()
        ->assertSee('Public Approved Event')
        ->assertDontSee('Secret Pending Event')
        ->assertDontSee('Secret Returned Event');
});

test('an office can edit a request returned for revision', function () {
    $office = User::factory()->create(['role' => 'office', 'email' => 'osa@psu.local']);
    $request = dashboardEvent(['status' => 'returned']);

    $this->actingAs($office)->get("/office/requests/{$request->id}/edit")
        ->assertSessionHas('info')
        ->assertSessionMissing('error');
    $this->actingAs($office)->get('/office')->assertOk()->assertSee('Returned for revision');
});
