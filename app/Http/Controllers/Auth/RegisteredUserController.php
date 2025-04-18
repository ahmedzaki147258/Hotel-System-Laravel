<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Lwwcas\LaravelCountries\Models\Country;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register', [
            'countries' => Country::with('translations')
            ->where('is_visible', 1)
            ->get()
            ->sortBy('name')
            ->values()
            ->map(function($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'iso_alpha_2' => $country->iso_alpha_2
                ];
            })
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:clients',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'gender' => 'required|in:Male,Female',
            'country_id' => 'required|exists:lc_countries,id',
            'mobile' => 'required|string',
            'avatar_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'country_id' => $request->country_id,
            'mobile' => $request->mobile,
            'avatar_image' => $request->file('avatar_image')->store('clients', 'public'),
        ]);

        event(new Registered($client));

        return to_route('login');
    }
}
