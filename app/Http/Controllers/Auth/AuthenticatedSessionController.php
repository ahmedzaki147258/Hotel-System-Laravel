<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CountryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Cookie;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse | Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        $token = session('sanctum_token');

        if ($request->userType === 'client') {
            $client = Client::where('email', $request->email)->first();
            $client->last_login_at = now();
            $client->save();

            return Inertia::render('ClientDashboard', [
                'token' => $token,
                'countries' => CountryHelper::getVisibleCountries()
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Determine which guard to use based on which one is authenticated
        if (Auth::guard('client')->check()) {
            Auth::guard('client')->logout();
        } else {
            Auth::guard('web')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
