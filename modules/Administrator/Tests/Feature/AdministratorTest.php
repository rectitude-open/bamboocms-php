<?php

use Modules\Administrator\Domain\Models\Administrator;

it('cannot login if not logged in', function () {
    $response = $this->getJson('/api/adminstrators');
    $response->assertStatus(401);
});

it('can login if logged in', function () {
    $user = Administrator::factory()->create();
    $response = $this->actingAs($user)->getJson('/api/adminstrators');
    $response->assertStatus(200);
});
