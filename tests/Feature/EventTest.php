<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessEvent;
use Tests\TestCase;

class EventTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        Queue::fake();
       
        $response =  $this->post('/api/events', ['event' => 'user_registered',
            'data' => ['user_id' => 1, 'email' => 'user@example.com']]);

        Queue::assertPushed(ProcessEvent::class);
        
        $response->assertStatus(200);
    }
}
