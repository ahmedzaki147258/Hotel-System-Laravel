<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Show the password reset page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:clients,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        // $status = Password::reset(
        //     $request->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user) use ($request) {
        //         $user->forceFill([
        //             'password' => Hash::make($request->password),
        //             'remember_token' => Str::random(60),
        //         ])->save();

        //         event(new PasswordReset($user));
        //     }
        // );

        $client = Client::where('email', $request->email)->first();
        if (!$client) {
            throw ValidationException::withMessages([
                'email' => trans('account deleted'),
            ]);
        }
        if (!$client->approved_by || !$client->approved_at) {
            throw ValidationException::withMessages([
                'email' => trans('account inactive'),
            ]);
        }

        if (!$client || $client->reset_token !== $request->token) {
            return back()->withErrors(['email' => 'The provided token is invalid.']);
        }
        $client->password = Hash::make($request->password);
        $client->reset_token = null;
        $client->save();

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        // if ($status == Password::PasswordReset) {
        //     return to_route('login')->with('status', __($status));
        // }

        // throw ValidationException::withMessages([
        //     'email' => [__($status)],
        // ]);
        return redirect()->route('login')->with('status', 'Your password has been reset!');
    }
}
