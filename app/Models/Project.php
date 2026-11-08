<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Project extends Model
{
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
