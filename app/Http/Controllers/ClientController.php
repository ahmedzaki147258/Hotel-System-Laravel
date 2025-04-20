<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryResource;
use App\Http\Resources\ReservationResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
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

    /*************************************** Relations ***************************************/
    public function getReservations(Request $request)
    {
        $limit = $request->limit ?? 10;
        $page = $request->page ?? 1;
        $user = $request->user();
        $reservations = $user->reservations()->paginate($limit, ['*'], 'page', $page);
        return ReservationResource::collection($reservations);
    }
}
