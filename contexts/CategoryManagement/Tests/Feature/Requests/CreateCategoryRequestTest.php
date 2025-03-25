<?php

declare(strict_types=1);

beforeEach(function () {
    $this->loginAsUser();
});

it('[smoke test] requires a label field', function () {
    $response = $this->postJson('categories', [
        'body' => 'This is test body',
    ]);

    $this->assertValidationError($response, 'label', 'required');
});
