<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'project_id'])]
class Task extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->priority = (static::max('priority') ?? 0) + 1;
        });

        // Bulk deletes load all selected tasks before deleting them one by one,
        // so the priority we have in memory may already be out of date.
        static::deleting(function (Task $task) {
            $task->refresh();
        });

        static::deleted(function (Task $task) {
            static::where('priority', '>', $task->priority)->decrement('priority');
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
