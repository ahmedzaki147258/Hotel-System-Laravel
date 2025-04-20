<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Lwwcas\LaravelCountries\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    use AuthorizesRequests;
     /**
     * Display a listing of the clients.
     */
    public function index()
    {
        // Authorize the user to view clients
        $this->authorize('viewAny', Client::class);
        
        $staff = Auth::user();
        $clients = [];
        
        if ($staff->hasRole(['admin', 'manager'])) {
            // Managers and admins see all clients
            $clients = Client::with('country')->latest()->get();
        } elseif ($staff->hasRole('receptionist')) {
            // Receptionists only see clients that haven't been approved
            $clients = Client::whereNull('approved_by')->with('country')->latest()->get();
        }
        
        return Inertia::render('Clients/Index', [
            'clients' => $clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'mobile' => $client->mobile,
                    'country' => $client->country ? $client->country->name : null,
                    'gender' => $client->gender,
                    'approved_by' => $client->approved_by,
                    'approved_at' => $client->approved_at,
                ];
            }),
            'userRole' => $staff->roles->pluck('name')[0] ?? null,
        ]);
    }
    
    /**
     * Display the receptionist's approved clients.
     */
    public function myApprovedClients()
    {
        $staff = Auth::user();
        
        // Check if user has permission to view own approved clients
        $this->authorize('viewOwnApprovedClients', Client::class);
        
        $clients = Client::where('approved_by', $staff->id)
                        ->with('country')
                        ->latest()
                        ->get();
        
        return Inertia::render('Clients/ApprovedClients', [
            'clients' => $clients->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'mobile' => $client->mobile,
                    'country' => $client->country ? $client->country->name : null,
                    'gender' => $client->gender,
                    'approved_at' => $client->approved_at,
                ];
            }),
        ]);
    }

    /**
     * Approve a client
     */
    public function approve(Client $client)
    {
        $this->authorize('approveClient', Client::class);
        
        $staff = Auth::user();
        
        $client->update([
            'approved_by' => $staff->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Client approved successfully');
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        // Check if the user can view the client using the policy's 'view' method
        $this->authorize('view', $client);

        return Inertia::render('Clients/Show', [
            'client' => $client->load('country'),
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
        $this->authorize('create', Client::class);

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|min:8',
            'country_id' => 'required|exists:lc_countries,id',
            'gender' => 'required|in:Male,Female',
            'mobile' => 'required|string',
            'avatar_image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['password'] = Hash::make($request->input('password'));

        // Handle avatar image
        $imagePath = null;
        if ($request->hasFile('avatar_image')) {
            $imagePath = $request->file('avatar_image')->store('clients', 'public');
            $data['avatar_image'] = $imagePath;
        }

        // Auto-approve if manager or admin
        $staff = Auth::user();
        if ($staff->hasRole(['admin', 'manager'])) {
            $data['approved_by'] = $staff->id;
            $data['approved_at'] = now();
        }

        Client::create($data);

        return redirect()->route('clients.index')->with('success', 'Client created successfully');
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

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|min:8',
            'country_id' => 'required|exists:lc_countries,id',
            'gender' => 'required|in:Male,Female',
            'mobile' => 'required|string',
            'avatar_image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        
        // Handle password
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        
        // Handle avatar image
        if ($request->hasFile('avatar_image')) {
            if ($client->avatar_image) {
                Storage::disk('public')->delete($client->avatar_image);
            }
            $imagePath = $request->file('avatar_image')->store('clients', 'public');
            $data['avatar_image'] = $imagePath;
        } else {
            unset($data['avatar_image']);
        }

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully');
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        // Check if the user can delete the client
        $this->authorize('delete', $client);
        
        if ($client->avatar_image) {
            Storage::disk('public')->delete($client->avatar_image);
        }
        
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully');
    }
}
