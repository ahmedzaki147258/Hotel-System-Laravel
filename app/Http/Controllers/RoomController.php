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
         $currentStaff = Staff::find(auth()->id());
        // Check if the user has the 'manager' or 'admin' role
        if ($currentStaff->hasRole('admin')) {
            // Perform some transformations or mapping for admins
            $rooms = $rooms->map(function ($room) {
                $room->manager_name = Staff::find($room->manager_id)->name; // Assuming the function we defined earlier
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
        dd($request->all());
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
