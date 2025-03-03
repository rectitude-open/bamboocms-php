<?php

declare(strict_types=1);

it('can publish aritcle via api', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'body' => 'This is my article body',
        'status' => 'draft',
    ]);

    $response->assertStatus(201);
});
