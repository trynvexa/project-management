<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkspaceMember;

class WorkspaceMemberPolicy
{
    public function update(User $user, WorkspaceMember $member): bool
    {
        return $member->workspace_id === (int) session('current_workspace_id')
            && $user->workspaceMemberships()->where('workspace_id', $member->workspace_id)->where('role', 'owner')->where('status', 'active')->exists();
    }

    public function delete(User $user, WorkspaceMember $member): bool
    {
        return $member->role !== 'owner'
            && $member->workspace_id === (int) session('current_workspace_id')
            && $user->workspaceMemberships()->where('workspace_id', $member->workspace_id)->whereIn('role', ['owner', 'admin'])->where('status', 'active')->exists();
    }
}
