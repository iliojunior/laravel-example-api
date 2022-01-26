<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleCasesTest extends TestCase
{    
    private static string $uri = "/api/companies/37817411000184";

    public function test_should_get_and_save_company()
    {
        $response = $this->get(self::$uri);

        $response->assertOk();
    }

    public function test_should_update_company()
    {
        $response = $this->put(self::$uri);

        $response->assertOk();
    }

    public function test_should_delete_company()
    {
        $response = $this->delete(self::$uri);

        $response->assertNoContent();
    }

    public function test_should_throw_400_on_delete()
    {
        $response = $this->delete("/api/companies/wrongCnpj");

        $response->assertStatus(400);
        $response->assertJson([
            "message" => "The input cnpj is invalid",
        ]);
    }

}
