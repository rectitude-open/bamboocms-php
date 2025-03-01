<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected array $routes = [];

    protected string $tableName = '';

    protected function getRoute($name, $params = [])
    {
        $routeName = $this->routes[$name] ?? null;
        if (! $routeName) {
            throw new \Exception("Route name not found: $name");
        }

        return route($routeName, $params);
    }

    public function assertValidationError($response, $field, $messageContains = null)
    {
        $response->assertStatus(422);
        $error = $response->json('error.message');

        expect($error)->toContain($field);

        if ($messageContains) {
            expect($error)->toContain($messageContains);
        }

        return $response;
    }
}
