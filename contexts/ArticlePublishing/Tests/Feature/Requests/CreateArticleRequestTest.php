<?php

declare(strict_types=1);

beforeEach(function () {
    $this->loginAsUser();
});

it('[smoke test] requires a title field', function () {
    $response = $this->postJson('articles', [
        'body' => 'This is test body',
    ]);

    $this->assertValidationError($response, 'title', 'required');
});
