<?php

declare(strict_types=1);

it('can publish aritcle via api', function () {
    $response = $this->postJson('articles', [
        'title' => 'My Article',
        'content' => 'This is my article content',
    ]);

    $response->assertStatus(201);
});
