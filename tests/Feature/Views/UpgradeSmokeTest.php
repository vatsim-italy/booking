<?php

use App\Models\Event;
use App\Models\User;
use Tests\TestCase;

it('renders the Livewire slot table', function () {
    /** @var TestCase $this */
    $event = Event::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('bookings.event.index', $event))
        ->assertOk()
        ->assertSeeLivewire('bookings')
        ->assertSee($event->name);
});

it('renders the admin event form with form components and date pickers', function () {
    /** @var TestCase $this */
    $event = Event::factory()->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.events.edit', $event))
        ->assertOk()
        ->assertSee('name="startEvent"', false)
        ->assertSee('value="' . e($event->name) . '"', false);
});
