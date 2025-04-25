<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class FloorController extends Controller
{
    public function index(Request $request)
    {      
        $query = Floor::query();
        $floors = $query->paginate($request->input('per_page', 5));
        $transformedData = $floors->getCollection()->map(function ($floor) {
            return [
                'id' => $floor->id,
                'name' => $floor->name,
                'number' => $floor->number,
                'room_count' => $floor->rooms()->count(),
                'manager_name' => optional(Staff::find($floor->manager_id))->name ?? 'No Manager',
                'manager_id' => $floor->manager_id,
            ];
        });
        $paginatedFloors = new LengthAwarePaginator(
            $transformedData,
            $floors->total(),
            $floors->perPage(),
            $floors->currentPage(),
            ['path' => $request->url()]
        );
        $currentStaff = Staff::find(auth()->id());
        return Inertia::render('Floors/Index', [
            'floors' => $paginatedFloors,
            'isManager' => $currentStaff->hasRole('manager'),
            'isAdmin' => $currentStaff->hasRole('admin'),
            'currentId' => $currentStaff->id,
        ]);
    }

    public function create()
    {
        $lastFloorNumber = Floor::max('number');
        $nextFloorNumber = $lastFloorNumber ? $lastFloorNumber + 1000 : 1000;
        $currentStaff = Staff::find(auth()->id());
        return Inertia::render('Floors/Create', [
            'managerId' => auth()->id(),
            'nextFloorNumber' => $nextFloorNumber,
        ]);
    }

    public function store(Request $request)
    {   
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'number' => 'required|numeric|digits:4|unique:floors,number',
            'manager_id' => 'required|exists:staff,id',
        ]);
        Floor::create([
            'name' => $request->name,
            'number' => $request->number,
            'manager_id' => $request->manager_id,
        ]);
        return redirect()->route('floors.index')->with('success', 'Floor created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        return Inertia::render('Floors/Edit', [
            'floor' => Floor::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:3',
        ]);
        $floor = Floor::find($id);
        $floor->update($validatedData);
        return redirect()->route('floors.index');
    }

    public function destroy(string $id)
    {
        $floor = Floor::findOrFail($id);
        if ($floor->rooms()->exists()) {
            return redirect()->route('floors.index')->with('error', 'Cannot delete floor with associated rooms.');
        }
        $floor->delete();
        return redirect()->route('floors.index')->with('success', 'Floor deleted successfully.');
    }
}
