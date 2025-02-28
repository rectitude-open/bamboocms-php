<?php

declare(strict_types=1);

namespace Contexts\ArticlePublishing\Infrastructure\Records;

use App\Http\Models\BaseModel;

class ArticleRecord extends BaseModel
{
    protected $table = 'articles';
    protected $fillable = ['title', 'content'];
}
