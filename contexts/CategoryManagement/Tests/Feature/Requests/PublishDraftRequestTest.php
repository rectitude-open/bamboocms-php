<?php

declare(strict_types=1);

it('[smoke test] id must greater than 0', function () {
    $response = $this->putJson('categories/-1/publish');

    $this->assertValidationError($response, 'id', 'greater than');
});
