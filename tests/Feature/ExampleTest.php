<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_application_returns_successful_response()
    {
        $response = $this->get('/catalog'); // Ajustez le chemin si nécessaire

        $response->assertStatus(200); // Si un code 302 est attendu, gérez-le en conséquence
    }
}
