<?php

declare(strict_types=1);

it('[smoke test] requires a title field', function () {
    $response = $this->postJson('articles', [
        'content' => 'This is test content',
    ]);

    $this->assertValidationError($response, 'title', 'required');
});
