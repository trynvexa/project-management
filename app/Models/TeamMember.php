<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWorkspaceOwner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use BelongsToWorkspaceOwner;
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'user_id',
        'name',
        'email',
        'role',
        'position',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
