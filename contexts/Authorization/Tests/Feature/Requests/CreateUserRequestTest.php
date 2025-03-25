<?php

declare(strict_types=1);

beforeEach(function () {
    $this->loginAsUser();
});

it('[smoke test] requires a email field', function () {
    $response = $this->postJson('users', []);

    $this->assertValidationError($response, 'email', 'required');
});
