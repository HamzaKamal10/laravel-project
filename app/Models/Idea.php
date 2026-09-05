<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\IdeaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idea extends Model
{
    /** @use HasFactory<IdeaFactory> */
    use HasFactory;
}
