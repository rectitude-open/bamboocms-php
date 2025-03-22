<?php

declare(strict_types=1);

namespace Contexts\Shared\Infrastructure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRecord extends Model
{
    use HasFactory;
}
