<?php

namespace App\Http\Controllers;

use App\Models\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    /**
     * Menampilkan semua anggota team.
     */
    public function index(Request $request)
    {
        $query = WorkspaceMember::with('user')->where('workspace_id', session('current_workspace_id'));

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($users) => $users->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $team = $query
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('team.index', compact('team'));
    }

    /**
     * Menampilkan form tambah anggota team.
     */
    public function create()
    {
        return view('team.create');
    }

    /**
     * Menyimpan anggota team baru.
     */
    public function store(Request $request)
    {
        return redirect()->route('team.create');
    }

    /**
     * Menampilkan form edit anggota team.
     */
    public function edit(WorkspaceMember $member)
    {
        $this->ensureCurrentWorkspace($member);

        return view('team.edit', compact('member'));
    }

    /**
     * Mengupdate anggota team.
     */
    public function update(Request $request, WorkspaceMember $member)
    {
        $this->ensureCurrentWorkspace($member);
        Gate::authorize('update', $member);
        $validated = $request->validate(['role' => ['required', 'in:admin,member']]);
        $actorRole = $request->user()->workspaceMemberships()->where('workspace_id', session('current_workspace_id'))->value('role');
        abort_if($actorRole !== 'owner', 403, 'Only an owner can change roles.');

        $member->update($validated);

        return redirect()
            ->route('team')
            ->with('success', 'Team member berhasil diperbarui.');
    }

    /**
     * Menghapus anggota team.
     */
    public function destroy(WorkspaceMember $member)
    {
        $this->ensureCurrentWorkspace($member);
        Gate::authorize('delete', $member);
        $member->delete();

        return redirect()
            ->route('team')
            ->with('success', 'Team member berhasil dihapus.');
    }

    /**
     * Aturan validasi bersama untuk store & update.
     */
    private function ensureCurrentWorkspace(WorkspaceMember $member): void
    {
        abort_unless($member->workspace_id === (int) session('current_workspace_id'), 404);
    }
}
