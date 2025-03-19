<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
   public function test_application_returns_successful_response()
{
    $response = $this->get('/catalog'); // Adjust the route if necessary

    $response->assertStatus(200); // If 302 is expected, handle it accordingly
} $response->assertStatus(200);
}

}
