<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('floor');
        $rooms = $query->paginate($request->input('per_page', 5));
        $transformedData = $rooms->getCollection()->map(function ($room) {
            return [
                'id' => $room->id,
                'number' => $room->number,
                'capacity' => $room->capacity,
                'price' => $room->price,
                'status' => $room->status,
                'floor_name' => optional($room->floor)->name ?? 'No Floor',
                'manager_name' => optional(Staff::find($room->manager_id))->name ?? 'N/A',
                'manager_id' => $room->manager_id,
            ];
        });
        $paginatedRooms = new LengthAwarePaginator(
            $transformedData,
            $rooms->total(),
            $rooms->perPage(),
            $rooms->currentPage(),
            ['path' => $request->url()]
        );
        $currentStaff = Staff::find(auth()->id());
        return Inertia::render('Rooms/Index', [
            'rooms' => $paginatedRooms,
            'isManager' => $currentStaff->hasRole('manager'),
            'isAdmin' => $currentStaff->hasRole('admin'),
            'currentId' => $currentStaff->id,
        ]);
    }

    public function create()
    {
        $currentStaff = Staff::find(auth()->id());
        $floors = Floor::all();
        return Inertia::render('Rooms/Create', [
            'managerId' => auth()->id(),
            'floors' => $floors,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'capacity' => 'required|numeric|min:1|max:5',
            'price' => 'required|numeric|min:0.5',
            'manager_id' => 'required|exists:staff,id',
            'floor_id' => 'required|exists:floors,id',
        ]);
        $floorId = $request->input('floor_id');
        $rooms = Room::where('floor_id', $floorId)->get();
        $maxRoomNumber = 0;
        if ($rooms->isEmpty()) {
            $maxRoomNumber = Floor::find($floorId)->number;
        } else {
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
            'price' => $request->price * 100,
            'manager_id' => $request->manager_id,
            'floor_id' => $floorId,
            'status' => 'available',
            'number' => $nextRoomNumber,
        ]);
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        return Inertia::render('Rooms/Edit', [
            'room' => Room::findOrFail($id),
            'floors' => Floor::all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'capacity' => 'required|numeric|min:1|max:5',
            'price' => 'required|numeric|min:0.5',
            'floor_id' => 'required|exists:floors,id',
        ]);
        $validatedData['price'] = $validatedData['price'] * 100;
        $room = Room::find($id);
        $room->update($validatedData);
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(string $id)
    {
        $room = Room::findOrFail($id);
        if ($room->status == 'unavailable') {
            return redirect()->route('rooms.index')->with('error', 'Room cannot be deleted while unavailable.');
        }
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
