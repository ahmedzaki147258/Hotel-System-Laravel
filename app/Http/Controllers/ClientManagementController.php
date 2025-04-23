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

class ClientManagementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the clients.
     */
    public function index(Request $request)
    {
        // Authorize the user to view clients
        $this->authorize('viewAny', Client::class);

        $staff = Auth::user();
        $pageSize = $request->input('pageSize', 5);
        $pageIndex = $request->input('pageIndex', 0);
        $canCreate = false;
        $canDelete = false;

        $query = null;

        if ($staff->hasPermissionTo('view all clients')) {
            // Admins and managers see all clients
            $query = Client::with('country')->latest();
            $canCreate = $staff->hasPermissionTo('create clients');
            $canDelete = $staff->hasPermissionTo('delete clients');
        } elseif ($staff->hasPermissionTo('view pending clients')) {
            // Receptionists only see pending clients
            $query = Client::whereNull('approved_by')->with('country')->latest();
            $canCreate = false;
            $canDelete = false;
        }

        $total = $query->count();
        $clients = $query->skip($pageIndex * $pageSize)
            ->take($pageSize)
            ->get();

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
            'pagination' => [
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
                'pageCount' => ceil($total / $pageSize),
                'rowCount' => $total,
            ],
            'userRole' => $staff->roles->pluck('name')[0] ?? null,
            'canCreate' => $canCreate,
            'canDelete' => $canDelete,
            'isReceptionist' => $staff->hasRole('receptionist'),
            'isAdmin' => $staff->hasRole('admin'),
            'isManager' => $staff->hasRole('manager'),
        ]);
    }

    /**
     * Display the approved clients.
     */
    public function myApprovedClients(Request $request)
    {
        $staff = Auth::user();

        // Check if user has permission to view own approved clients
        $this->authorize('viewOwnApprovedClients', Client::class);

        $pageSize = $request->input('pageSize', 5);
        $pageIndex = $request->input('pageIndex', 0);

        $query = Client::where('approved_by', $staff->id)
            ->with('country')
            ->latest();

        $total = $query->count();
        $clients = $query->skip($pageIndex * $pageSize)
            ->take($pageSize)
            ->get();

        $canEdit = $staff->hasRole('receptionist');

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
            'pagination' => [
                'pageIndex' => $pageIndex,
                'pageSize' => $pageSize,
                'pageCount' => ceil($total / $pageSize),
                'rowCount' => $total,
            ],
            'canEdit' => $canEdit,
            'userRole' => $staff->roles->pluck('name')[0] ?? null,
        ]);
    }

    /**
     * Approve a client
     */
    public function approve(Client $client)
    {
        $this->authorize('approveClient', Client::class);

        // Don't allow approving already approved clients
        if ($client->approved_by !== null) {
            return redirect()->back()->with('error', 'Client has already been approved');
        }

        $staff = Auth::user();

        $client->update([
            'approved_by' => $staff->id,
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Client approved successfully');
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
