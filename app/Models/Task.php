<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspaceOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use BelongsToWorkspaceOwner;
    use HasFactory;

    protected $fillable = [
        'project_id',
        'assignee_id',
        'name',
        'project', // kolom lama, tetap dipertahankan
        'description',
        'member', // kolom lama, tetap dipertahankan
        'deadline',
        'priority',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function projectRelation(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function isOverdue(): bool
    {
        return $this->deadline
            && $this->deadline->isPast()
            && ! in_array($this->status, ['Completed', 'Done'], true);
    }
}
