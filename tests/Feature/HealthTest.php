<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    /**
     * A basic health test
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        $view = $this->view('welcome');
        $view->assertSee('Laravel');
    }
}
