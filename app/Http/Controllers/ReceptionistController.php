<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Cog\Laravel\Ban\Traits\Bannable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;


class ReceptionistController extends Controller
{
    use AuthorizesRequests, Bannable;

    /**
     * Display a listing of the receptionists.
     */

    public function index(Request $request)
    {
        $receptionists = Staff::role('receptionist')->with('manager')->paginate($request->input('per_page', 5));
        // Transform the data using map()

        $transformedData = $receptionists->getCollection()->map(function ($receptionist) {

            return [
                'id' => $receptionist->id,
                'name' => $receptionist->name ? $receptionist->name : "DEFAULT_NAME",
                'email' => $receptionist->email,
                'national_id' => $receptionist->national_id,
                'avatar' => $receptionist->avatar_image
                    ? Storage::url($receptionist->avatar_image)
                    : Storage::url('receptionists/default-avatar.png'),
                'is_banned' => $receptionist->isBanned(),
                'created_at' => $receptionist->created_at,
                'manager' => optional($receptionist->manager)->name ?? 'DEFAULT_NAME',
                'manager_id' => $receptionist->manager_id,
            ];
        });

        $receptionists = new LengthAwarePaginator(
            $transformedData,
            $receptionists->total(),
            $receptionists->perPage(),
            $receptionists->currentPage(),
            ['path' => $request->url()]
        );
        $isAdmin =  Auth::user()->hasRole('admin');
        $isManager =  Auth::user()->hasRole('manager');

        return Inertia::render('Receptionists/Index', [
            'receptionists' => $receptionists,
            'isAdmin' => $isAdmin,
            'isManager' => $isManager,
            'currentManagerId' => Auth::id(),
        ]);
    }
    /**
     * Show the form for creating a new receptionist.
     */
    public function create()
    {

        return Inertia::render('Receptionists/Create');
    }

    /**
     * Store a newly created receptionist in storage.
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'national_id' => 'required|string|unique:staff,national_id',
            'password' => 'required|string|min:6',
            'avatar_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'manager_id' => 'nullable'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['manager_id'] = auth()->id();

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')->store('receptionists', 'public');
        } else {
            $data['avatar_image'] = 'receptionists/default-avatar.jpg';

            if (!Storage::disk('public')->exists($data['avatar_image'])) {
                Storage::disk('public')->put(
                    $data['avatar_image'],
                    Storage::url('receptionists/default-avatar.png')
                );
            }
        }


        $receptionist = Staff::create($data);
        $receptionist->assignRole('receptionist');


        return to_route('receptionists.index')
            ->with('success', 'Receptionist created successfully.');
    }

    /**
     * Display the specified receptionist.
     */
    public function show(Staff $receptionist)
    {

        return Inertia::render('Receptionists/Show', [
            'receptionist' => [
                'id' => $receptionist->id,
                'name' => $receptionist->name,
                'email' => $receptionist->email,
                'national_id' => $receptionist->national_id,
                'avatar' => $receptionist->avatar_image ? Storage::url($receptionist->avatar_image) : Storage::url('receptionists/default-avatar.png')

            ]
        ]);
    }

    /**
     * Show the form for editing the specified receptionist.
     */
    public function edit(Staff $receptionist)
    {

        return Inertia::render('Receptionists/Edit', [
            'receptionist' => [
                'id' => $receptionist->id,
                'name' => $receptionist->name,
                'email' => $receptionist->email,
                'national_id' => $receptionist->national_id,
                'avatar' => $receptionist->avatar_image ? Storage::url($receptionist->avatar_image) : Storage::url('receptionists/default-avatar.png')

            ]
        ]);
    }

    /**
     * Update the specified receptionist in storage.
     */
    public function update(Request $request, Staff $receptionist)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $receptionist->id,
            'national_id' => 'required|string|unique:staff,national_id,' . $receptionist->id,
            'password' => 'nullable|string|min:6',
            'avatar_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle password update if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        // Handle avatar image upload and cleanup
        if ($request->hasFile('avatar_image')) {
            // Delete old avatar if it's not the default one
            if (
                $receptionist->avatar_image &&
                $receptionist->avatar_image !== 'receptionists/default-avatar.jpg' &&
                Storage::disk('public')->exists($receptionist->avatar_image)
            ) {
                Storage::disk('public')->delete($receptionist->avatar_image);
            }

            // Store the new avatar
            $data['avatar_image'] = $request->file('avatar_image')->store('receptionists', 'public');
        }

        // Update the receptionist record
        try {
            $receptionist->update($data);

            return to_route('receptionists.index')->with('success', 'Receptionist updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified receptionist from storage.
     */
    public function destroy(Staff $receptionist)
    {
        // $this->authorize('delete', $receptionist);

        if (
            $receptionist->avatar_image &&
            $receptionist->avatar_image !== 'receptionists/default-avatar.jpg' &&
            Storage::disk('public')->exists($receptionist->avatar_image)
        ) {
            Storage::disk('public')->delete($receptionist->avatar_image);
        }

        $receptionist->delete();

        return to_route('receptionists.index')
            ->with('success', 'Receptionist deleted successfully.');
    }
    public function ban(Staff $receptionist)
    {
        $receptionist->ban();
        return back()->with('success', 'Receptionist banned successfully.');
    }

    public function unban(Staff $receptionist)
    {
        $receptionist->unban();
        return back()->with('success', 'Receptionist unbanned successfully.');
    }
}
