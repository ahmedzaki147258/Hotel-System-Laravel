<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */ public function index()
    {      
        $rooms = Room::all();
        $rooms = $rooms->map(function ($room) {
            $room->floor_name = Floor::find($room->floor_id)->name; 
            return $room;
        });
         $currentStaff = Staff::find(auth()->id());
        // Check if the user has the 'manager' or 'admin' role
        if ($currentStaff->hasRole('admin')) {
            // Perform some transformations or mapping for admins
            $rooms = $rooms->map(function ($room) {
                $room->manager_name = Staff::find($room->manager_id)->name; 
                return $room;
            });
        }
        return Inertia::render('Rooms/Index', [
            'rooms' => $rooms,
            'isManager' => $currentStaff->hasRole('manager'),
            'isAdmin' => $currentStaff->hasRole('admin'),
            'currentId' => $currentStaff->id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $currentStaff = Staff::find(auth()->id());
        $floors = Floor::all();
        return Inertia::render('Rooms/Create', [
            'managerId' => auth()->id(),
            'floors' => $floors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'capacity' => 'required|numeric|min:1|max:5', // Ensure capacity is at least 1
        'price' => 'required|numeric|min:0.5', // Ensure price is a positive number
        'manager_id' => 'required|exists:staff,id', // Ensure manager exists
        'floor_id' => 'required|exists:floors,id', // Ensure floor exists
    ]);
    $floorId = $request->input('floor_id');

    $rooms = Room::where('floor_id', $floorId)->get();
        $maxRoomNumber = 0;
        // Find the maximum room number for the selected floor
        if($rooms->isEmpty()) {
            $maxRoomNumber = Floor::find($floorId)->number; 
        } else{
            foreach ($rooms as $room) {
                if ($room->number > $maxRoomNumber) {
                    $maxRoomNumber = $room->number;
                }
            }
        }
        $nextRoomNumber = $maxRoomNumber + 1; 
        Room::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'price' => $request->price*100, // Store price in cents
            'manager_id' => $request->manager_id, // Store manager ID
            'floor_id' => $floorId, // Store floor ID
            'status' => 'available', // Set default status to available
            'number' => $nextRoomNumber, // Store the next room number
        ]);
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Inertia::render('Rooms/Edit', [
            'room' => Room::findOrFail($id),
            'floors' => Floor::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'capacity' => 'required|numeric|min:1|max:5', // Ensure capacity is at least 1
            'price' => 'required|numeric|min:0.5', // Ensure price is a positive number
            'floor_id' => 'required|exists:floors,id', // Ensure floor exists
        ]);
        $validatedData['price'] = $validatedData['price'] * 100; // Store price in cents
        $room = Room::find($id);
        $room->update($validatedData);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $room = Room::findOrFail($id);
       if($room->status == 'unavailable') {
            return redirect()->route('rooms.index')->with('error', 'Room cannot be deleted while unavailable.');
        }
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
