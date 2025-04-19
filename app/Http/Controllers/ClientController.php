<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClientController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the clients.
     */
    public function index()
    {

        // Automatically checks if the user can 'viewAny' and filters clients based on policy logic
        $this->authorize('viewAny', Client::class);

        $clients = Client::latest()->get(); // No need to filter here

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        // Check if the user can view the client using the policy's 'view' method
        $this->authorize('view', $client);

        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        // Check if the user can create a client
        $this->authorize('create', Client::class);

        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        // Check if the user can create a client
        $this->authorize('create', Client::class);

        // Validation and saving logic
        $client = Client::create($request->all());

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        // Check if the user can update the client
        $this->authorize('update', $client);

        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Check if the user can update the client
        $this->authorize('update', $client);

        // Validation and update logic
        $client->update($request->all());

        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        // Check if the user can delete the client
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
