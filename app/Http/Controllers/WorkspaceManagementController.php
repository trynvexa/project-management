<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkspaceManagementController extends Controller
{
    public function create()
    {
        return view('workspaces.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        $workspace = DB::transaction(function () use ($data, $request) {
            $slug = Str::slug($data['name']) ?: 'workspace';
            $base = $slug;
            $i = 2;
            while (Workspace::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }
            $workspace = Workspace::create(['name' => $data['name'], 'slug' => $slug, 'owner_id' => $request->user()->id]);
            WorkspaceMember::firstOrCreate(['workspace_id' => $workspace->id, 'user_id' => $request->user()->id], ['role' => 'owner', 'status' => 'active']);

            return $workspace;
        });
        $request->session()->put('current_workspace_id', $workspace->id);

        return redirect()->route('dashboard')->with('success', 'Workspace created.');
    }

    public function switch(Request $request, Workspace $workspace)
    {
        abort_unless($request->user()->workspaces()->whereKey($workspace->id)->wherePivot('status', 'active')->exists(), 403);
        $request->session()->put('current_workspace_id', $workspace->id);

        return back()->with('success', 'Workspace switched.');
    }
}
