<?php

namespace App\Http\Controllers;

use App\Http\Resources\FloorResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\RoomResource;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

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

    /*************************************** Make Reservation ***************************************/
    public function getFloors()
    {
        $floors = Floor::all();
        return FloorResource::collection($floors);
    }

    public function getRoomsByFloor($floorId)
    {
        $floor = Floor::find($floorId);
        $rooms = $floor->rooms;
        return RoomResource::collection($rooms);
    }

    public function makeReservation(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'accompany_number' => 'required|integer|min:1',
            'check_out_at' => 'required|date|after:now',
        ]);

        $room = Room::find($request->room_id);
        if ($room->status !== 'available') {
            return response()->json(['message' => 'Room is not available'], 400);
        }
        if ($room->capacity < $request->accompany_number) {
            return response()->json(['message' => 'Room capacity is not enough'], 400);
        }

        $checkOut = Carbon::parse($request->check_out_at);
        $days = now()->startOfDay()->diffInDays($checkOut->startOfDay());
        $totalPrice = $room->price * max(1, $days);
        session([
            'reservation_details' => [
                'room_id' => $request->room_id,
                'roomNumber' => $room->number,
                'accompany_number' => $request->accompany_number,
                'check_out_at' => $request->check_out_at,
                'price' => $totalPrice,
                'days' => max(1, $days)
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation details saved. Redirecting to payment.'
        ]);
    }

    public function storeReservation(Request $request)
    {
        if (!session()->has('reservation_details')) {
            return redirect()->route('dashboard')->with('error', 'Invalid reservation data');
        }

        $details = session('reservation_details');
        $room = Room::find($details['room_id']);
        if ($room->status !== 'available') {
            return redirect()->route('dashboard')->with('error', 'Room is no longer available');
        }

        $paymentId = $request->query('payment_id');
        if (empty($paymentId)) {
            return redirect()->route('dashboard')->with('error', 'Payment information missing');
        }

        $checkOutDate = Carbon::parse($details['check_out_at'])->format('Y-m-d H:i:s');
        Reservation::create([
            'client_id' => $request->user()->id,
            'room_id' => $details['room_id'],
            'payment_id' => (string) $paymentId,
            'accompany_number' => $details['accompany_number'],
            'paid_price_in_cents' => $details['price'],
            'check_out_at' => $checkOutDate,
        ]);

        $room->update(['status' => 'unavailable']);
        session()->forget('reservation_details');
        return redirect()->route('client.dashboard')->with('success', 'Reservation completed successfully');
    }
}
