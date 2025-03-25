<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;

class AuthenticateMiddleware extends Authenticate
{
    protected function redirectTo(Request $request) {}
}
