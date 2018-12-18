<?php

namespace Tests\Unit;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class EventTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if a event can be added
     *
     * @return void
     */
    public function testItCreatesNewEvent()
    {
        $event = factory(\App\Models\Event::class)->make();

        Event::create($event->toArray());

        $this->assertDatabaseHas('events', $event->toArray());
    }
}
