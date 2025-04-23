<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FloorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {      
        $floors = Floor::all();
         $currentStaff = Staff::find(auth()->id());
        // check if the floor has any associated rooms
        $floors = $floors->map(function ($floor) {
            $floor->room_count = $floor->rooms()->count();
            return $floor;
        });
        // Check if the user has the 'manager' or 'admin' role
        if ($currentStaff->hasRole('admin')) {
            // Perform some transformations or mapping for admins
            $floors = $floors->map(function ($floor) {
                $floor->manager_name = Staff::find($floor->manager_id)->name; // Assuming the function we defined earlier
                return $floor;
            });
        }
        return Inertia::render('Floors/Index', [
            'floors' => $floors,
            'isManager' => $currentStaff->hasRole('manager'),
            'isAdmin' => $currentStaff->hasRole('admin'),
            'currentId' => $currentStaff->id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */public function create()
{
    $lastFloorNumber = Floor::max('number');
    $nextFloorNumber = $lastFloorNumber ? $lastFloorNumber + 1000 : 1000;
    $currentStaff = Staff::find(auth()->id());
    return Inertia::render('Floors/Create', [
        'managerId' => auth()->id(),
        'nextFloorNumber' => $nextFloorNumber,
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        $request->validate([
            'name' => 'required|string|max:255|min:3', // Ensure name is at least 3 characters
            'number' => 'required|numeric|digits:4|unique:floors,number', // Ensure unique and at least 4 digits for number
            'manager_id' => 'required|exists:staff,id', // Ensure manager exists
        ]);
        // Create the floor and associate it with the manager
        Floor::create([
            'name' => $request->name,
            'number' => $request->number,
            'manager_id' => $request->manager_id, // Store manager ID
        ]);
    
        return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
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
        return Inertia::render('Floors/Edit', [
            'floor' => Floor::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
        ]);
    
        // Find the floor by ID and update it
        $floor = Floor::find($id);
        $floor->update($validatedData);
    
        // Redirect or respond after update
        return redirect()->route('floors.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $floor = Floor::findOrFail($id);
        // Check if the floor has any associated rooms
        if ($floor->rooms()->exists()) {
            return redirect()->route('floors.index')->with('error', 'Cannot delete floor with associated rooms.');
        }
        $floor->delete();

        return redirect()->route('floors.index')->with('success', 'Floor deleted successfully.');
    }
}
