<?php

declare(strict_types=1);

beforeEach(function () {
    $this->loginAsUser();
});

it('[smoke test] id must greater than 0', function () {
    $response = $this->putJson('articles/-1/publish');

    $this->assertValidationError($response, 'id', 'greater than');
});
