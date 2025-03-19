<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
{
    // Désactiver tous les middlewares pour ce test
    $response = $this->withoutMiddleware()->get('/');

    // Vérifier que la réponse a un statut 200 (succès)
    $response->assertStatus(200);
}

}
