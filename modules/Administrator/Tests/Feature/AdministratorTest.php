<?php

it('has administrators page', function () {
    $response = $this->getJson('/api/adminstrators');
    $response->assertStatus(401);
});
