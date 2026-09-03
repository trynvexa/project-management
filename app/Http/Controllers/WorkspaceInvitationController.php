<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceInvitation;
use App\Models\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class WorkspaceInvitationController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255'], 'role' => ['required', 'in:admin,member']]);
        $workspaceId = (int) session('current_workspace_id');
        $actorRole = $request->user()->workspaceMemberships()->where('workspace_id', $workspaceId)->where('status', 'active')->value('role');
        abort_if($actorRole !== 'owner' && $data['role'] === 'admin', 403, 'Only an owner can invite an admin.');
        $rawToken = Str::random(64);
        $invitation = DB::transaction(function () use ($workspaceId, $data, $rawToken, $request) {
            abort_if(WorkspaceMember::where('workspace_id', $workspaceId)->whereHas('user', fn ($q) => $q->where('email', $data['email']))->exists(), 422, 'This user is already a member.');
            WorkspaceInvitation::where('workspace_id', $workspaceId)->where('email', strtolower($data['email']))->whereNull('accepted_at')->whereNull('declined_at')->lockForUpdate()->update(['expires_at' => now()]);

            return WorkspaceInvitation::create([
                'workspace_id' => $workspaceId, 'email' => strtolower($data['email']), 'role' => $data['role'],
                'token_hash' => hash('sha256', $rawToken), 'expires_at' => now()->addDays(7), 'invited_by' => $request->user()->id,
            ]);
        });
        $url = route('invitations.show', ['token' => $rawToken]);
        Mail::raw("You have been invited to a workspace. Accept or decline: {$url}", fn ($message) => $message->to($invitation->email)->subject('Workspace invitation'));

        return back()->with('success', 'Invitation sent.');
    }

    public function show(string $token)
    {
        return view('team.invitation', ['token' => $token]);
    }

    public function accept(Request $request, string $token)
    {
        return $this->respond($request, $token, true);
    }

    public function decline(Request $request, string $token)
    {
        return $this->respond($request, $token, false);
    }

    private function respond(Request $request, string $token, bool $accept)
    {
        $invitation = WorkspaceInvitation::where('token_hash', hash('sha256', $token))->firstOrFail();
        abort_unless($invitation->isUsable() && hash_equals(strtolower($invitation->email), strtolower($request->user()->email)), 403);
        DB::transaction(function () use ($invitation, $request, $accept): void {
            if ($accept) {
                WorkspaceMember::firstOrCreate(['workspace_id' => $invitation->workspace_id, 'user_id' => $request->user()->id], ['role' => $invitation->role, 'status' => 'active']);
            }
            $invitation->update([$accept ? 'accepted_at' : 'declined_at' => now()]);
        });
        if ($accept) {
            $request->session()->put('current_workspace_id', $invitation->workspace_id);
        }

        return redirect()->route($accept ? 'dashboard' : 'workspaces.create')->with('success', $accept ? 'Invitation accepted.' : 'Invitation declined.');
    }
}
