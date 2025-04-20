<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Lwwcas\LaravelCountries\Models\Country;

class ClientController extends Controller
{
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password changed successfully');
    }

    public function updateAvatarImage(Request $request)
    {
        $request->validate([
            'avatar_image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = $request->user();
        if ($request->hasFile('avatar_image')) {
            Storage::disk('public')->delete($user->avatar_image);
            $user->avatar_image = $request->file('avatar_image')->store('clients', 'public');
            $user->save();
        }

        return back()->with('success', 'Avatar updated successfully');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:clients,email,' . $request->user()->id,
            'gender' => 'required|in:Male,Female',
            'country_id' => 'required|exists:lc_countries,id',
            'mobile' => 'required|string|max:20',
        ]);

        $user = $request->user();
        $user->update($request->only(['name', 'email', 'mobile', 'country_id', 'gender']));
        return back()->with('success', 'Profile updated successfully');
    }

    public function deleteProfile(Request $request)
    {
        $user = $request->user();
        $user->update(['last_login_at' => null]);
        $user->tokens()->delete();
        $user->delete();
        return redirect('/login')->with('success', 'Account deleted successfully');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->update(['last_login_at' => now()]);
        $user->tokens()->delete();
        return redirect('/login')->with('success', 'Logged out successfully');
    }

    // use AuthorizesRequests;

    // /**
    //  * Display a listing of the clients.
    //  */
    // public function index()
    // {

    //     // Automatically checks if the user can 'viewAny' and filters clients based on policy logic
    //     $this->authorize('viewAny', Client::class);

    //     $clients = Client::latest()->get(); // No need to filter here

    //     return Inertia::render('Clients/Index', [
    //         'clients' => $clients,
    //     ]);
    // }

    // /**
    //  * Display the specified client.
    //  */
    // public function show(Client $client)
    // {
    //     // Check if the user can view the client using the policy's 'view' method
    //     $this->authorize('view', $client);

    //     return Inertia::render('Clients/Show', [
    //         'client' => $client,
    //     ]);
    // }

    // /**
    //  * Show the form for creating a new client.
    //  */
    // public function create()
    // {
    //     // Check if the user can create a client
    //     $this->authorize('create', Client::class);

    //     return Inertia::render('Clients/Create');
    // }

    // /**
    //  * Store a newly created client in storage.
    //  */
    // public function store(Request $request)
    // {
    //     // Check if the user can create a client
    //     $this->authorize('create', Client::class);

    //     // Validation and saving logic
    //     $client = Client::create($request->all());

    //     return redirect()->route('clients.index');
    // }

    // /**
    //  * Show the form for editing the specified client.
    //  */
    // public function edit(Client $client)
    // {
    //     // Check if the user can update the client
    //     $this->authorize('update', $client);

    //     return Inertia::render('Clients/Edit', [
    //         'client' => $client,
    //     ]);
    // }

    // /**
    //  * Update the specified client in storage.
    //  */
    // public function update(Request $request, Client $client)
    // {
    //     // Check if the user can update the client
    //     $this->authorize('update', $client);

    //     // Validation and update logic
    //     $client->update($request->all());

    //     return redirect()->route('clients.index');
    // }

    // /**
    //  * Remove the specified client from storage.
    //  */
    // public function destroy(Client $client)
    // {
    //     // Check if the user can delete the client
    //     $this->authorize('delete', $client);

    //     $client->delete();

    //     return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    // }
}
