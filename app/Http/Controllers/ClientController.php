<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Menampilkan semua client.
     */
    public function index(Request $request)
    {
        $query = Client::query()->withCount(['projects', 'tasks']);

        // Search client
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    /**
     * Menampilkan form tambah client.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Menyimpan client baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);

        Client::create($validated);

        return redirect()
            ->route('clients')
            ->with('success', 'Client berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail client.
     */
    public function show(Client $client)
    {
        $client->load('projects.tasks');

        return view('clients.show', compact('client'));
    }

    /**
     * Menampilkan form edit client.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Mengupdate client.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);

        $client->update($validated);

        return redirect()
            ->route('clients')
            ->with('success', 'Client berhasil diperbarui.');
    }

    /**
     * Menghapus client.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()
            ->route('clients')
            ->with('success', 'Client berhasil dihapus.');
    }
}
