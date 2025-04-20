<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Lwwcas\LaravelCountries\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
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

        return Inertia::render('Clients/Create', [
            'countries' => Country::with('translations')
                ->where('is_visible', 1)
                ->get()
                ->sortBy('name')
                ->values()
                ->map(function ($country) {
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                        'iso_alpha_2' => $country->iso_alpha_2
                    ];
                })
        ]);
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        // Check if the user can create a client
        // $this->authorize('create', Client::class);

        // Validation and saving logic
        $data = $request->all();

        $data['password'] = Hash::make($request->input('password'));

        // Validate the image if present
        $imagePath = null;
        if ($data['avatar_image']) {
            $imagePath = $request->file('avatar_image')->store('clients', 'public');
            $data['avatar_image'] = $imagePath;
        }


        Client::create($data);

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
            'countries' => Country::with('translations')
                ->where('is_visible', 1)
                ->get()
                ->sortBy('name')
                ->values()
                ->map(function ($country) {
                    return [
                        'id' => $country->id,
                        'name' => $country->name,
                        'iso_alpha_2' => $country->iso_alpha_2
                    ];
                })
        ]);
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Check if the user can update the client
        $this->authorize('update', $client);

        $data = $request->all();

        // Handle password
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $imagePath = null;
        if ($data['avatar_image']) {
            if ($client->avatar_image) {
                Storage::disk('public')->delete($client->avatar_image);
            }
            $imagePath = $request->file('avatar_image')->store('clients', 'public');
            $data['avatar_image'] = $imagePath;
        } else {
            unset($data['avatar_image']);
        }
        // Validation and update logic
        $client->update($data);

        return redirect()->route('clients.index');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        // Check if the user can delete the client
        //  $this->authorize('delete', $client);
        if ($client->avatar_image) {
            Storage::disk('public')->delete($client->avatar_image);
        }
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}
