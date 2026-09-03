<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Keeps workspace records private to the selected, authorised workspace.
 *
 * The scope also applies to implicit route model binding, so guessing an ID
 * from another account results in a 404 rather than exposing the record.
 */
trait BelongsToWorkspaceOwner
{
    protected static function bootBelongsToWorkspaceOwner(): void
    {
        static::addGlobalScope('workspace-owner', function (Builder $query): void {
            if (auth()->check()) {
                // A missing context must fail closed; middleware normally
                // establishes it before any business route is reached.
                $query->where($query->getModel()->qualifyColumn('workspace_id'), session('current_workspace_id', 0));
            }
        });

        static::creating(function ($model): void {
            if (auth()->check()) {
                $model->user_id ??= auth()->id();
                $model->workspace_id ??= session('current_workspace_id');
            }
        });
    }
}
