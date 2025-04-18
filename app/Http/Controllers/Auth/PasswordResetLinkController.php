<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Mail;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:clients,email',
                function ($attribute, $value, $fail) {
                    $client = \App\Models\Client::where('email', $value)->first();
                    if (!$client) {
                        return $fail(trans('account deleted'));
                    }
                    if (!$client->approved_by || !$client->approved_at) {
                        return $fail(trans('account inactive'));
                    }
                },
            ],
        ]);

        $client = Client::where('email', $request->email)->first();
        $client->reset_token = bin2hex(random_bytes(50));
        $client->save();
        Mail::to($request->email)->send(new ResetPassword($client));
        // Password::sendResetLink(
        //     $request->only('email')
        // );

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
