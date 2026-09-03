<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Menampilkan semua project.
     */
    public function index(Request $request)
    {
        $query = Project::with('clientRelation')->withCount('tasks');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status (poin 8: Search & Filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $projects = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('projects.index', compact('projects', 'clients'));
    }

    /**
     * Menampilkan form tambah project.
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();

        return view('projects.create', compact('clients'));
    }

    /**
     * Menyimpan project baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateProject($request);

        Project::create($validated);

        return redirect()
            ->route('projects')
            ->with('success', 'Project berhasil dibuat.');
    }

    /**
     * Menampilkan detail project.
     */
    public function show(Project $project)
    {
        $project->load(['clientRelation', 'tasks.assignee']);

        return view('projects.show', compact('project'));
    }

    /**
     * Menampilkan form edit project.
     */
    public function edit(Project $project)
    {
        $clients = Client::orderBy('name')->get();

        return view('projects.edit', compact('project', 'clients'));
    }

    /**
     * Mengupdate project.
     */
    public function update(Request $request, Project $project)
    {
        $validated = $this->validateProject($request);

        $project->update($validated);

        return redirect()
            ->route('projects')
            ->with('success', 'Project berhasil diperbarui.');
    }

    /**
     * Menghapus project.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()
            ->route('projects')
            ->with('success', 'Project berhasil dihapus.');
    }

    /**
     * Aturan validasi bersama untuk store & update.
     */
    private function validateProject(Request $request): array
    {
        $validated = $request->validate([
            'client_id' => ['nullable', Rule::exists('clients', 'id')->where('workspace_id', session('current_workspace_id'))],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:Website,Mobile App,Software,Graphic Design'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['required', 'date'],
            'status' => ['nullable', 'in:Planning,In Progress,Review,Completed'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
            'description' => ['nullable', 'string'],
        ]);

        // Kolom 'client' (teks lama) tetap diisi otomatis dari nama client yang dipilih,
        // biar tampilan lama yang masih pakai kolom ini tetap jalan normal.
        $validated['client'] = ! empty($validated['client_id'])
            ? Client::query()->find($validated['client_id'])?->name
            : null;

        return $validated;
    }
}
