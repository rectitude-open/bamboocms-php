<?php

declare(strict_types=1);

it('[smoke test] requires a label field', function () {
    $response = $this->postJson('roles', []);

    $this->assertValidationError($response, 'label', 'required');
});
