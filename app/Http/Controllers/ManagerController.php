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

class ManagerController extends Controller
{
    use AuthorizesRequests, Bannable;

    public function index(Request $request)
    {
        $managers = Staff::role('manager')->paginate($request->input('per_page', 5));
        
        // Transform the data using map()
        $transformedData = $managers->getCollection()->map(function ($manager) {
            return [
                'id' => $manager->id,
                'name' => $manager->name ? $manager->name : "DEFAULT_NAME",
                'email' => $manager->email,
                'national_id' => $manager->national_id,
                'avatar' => $manager->avatar_image
                    ? Storage::url($manager->avatar_image)
                    : Storage::url('managers/default-avatar.png'),
                'is_banned' => $manager->isBanned(),
                'created_at' => $manager->created_at,
            ];
        });

        $managers = new LengthAwarePaginator(
            $transformedData,
            $managers->total(),
            $managers->perPage(),
            $managers->currentPage(),
            ['path' => $request->url()]
        );

        return Inertia::render('Managers/Index', [
            'managers' => $managers,
        ]);
    }

    public function create()
    {
        return Inertia::render('Managers/Create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'national_id' => 'required|string|unique:staff,national_id',
            'password' => 'required|string|min:6',
            'avatar_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')->store('managers', 'public');
        } else {
            $data['avatar_image'] = 'managers/default-avatar.png';
            
            // Ensure default avatar exists
            if (!Storage::disk('public')->exists('managers/default-avatar.png')) {
                // Create managers directory if it doesn't exist
                if (!Storage::disk('public')->exists('managers')) {
                    Storage::disk('public')->makeDirectory('managers');
                }
                
                // Copy default avatar from public path to storage
                Storage::disk('public')->put(
                    'managers/default-avatar.png',
                    file_get_contents(public_path('images/default-avatar.jpg'))
                );
            }
        }

        $manager = Staff::create($data);
        $manager->assignRole('manager');

        return to_route('managers.index')
            ->with('success', 'Manager created successfully.');
    }

    public function edit(Staff $manager)
    {
        return Inertia::render('Managers/Edit', [
            'manager' => [
                'id' => $manager->id,
                'name' => $manager->name,
                'email' => $manager->email,
                'national_id' => $manager->national_id,
                'avatar' => $manager->avatar_image ? Storage::url($manager->avatar_image) : Storage::url('managers/default-avatar.png'),
            ]
        ]);
    }

    public function update(Request $request, Staff $manager)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'email|unique:staff,email,' . $manager->id,
            'national_id' => 'string|unique:staff,national_id,' . $manager->id,
            'password' => 'nullable|string|min:6',
            'avatar_image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar_image')) {
            if (
                $manager->avatar_image &&
                $manager->avatar_image !== 'managers/default-avatar.jpg' &&
                Storage::disk('public')->exists($manager->avatar_image)
            ) {
                Storage::disk('public')->delete($manager->avatar_image);
            }
            $data['avatar_image'] = $request->file('avatar_image')->store('managers', 'public');
        }

        $manager->update($data);

        return to_route('managers.index')
            ->with('success', 'Manager updated successfully.');
    }

    public function destroy(Staff $manager)
    {
        if (
            $manager->avatar_image &&
            $manager->avatar_image !== 'managers/default-avatar.png' &&
            Storage::disk('public')->exists($manager->avatar_image)
        ) {
            Storage::disk('public')->delete($manager->avatar_image);
        }

        $manager->delete();

        return to_route('managers.index')
            ->with('success', 'Manager deleted successfully.');
    }

    public function ban(Staff $manager)
    {
        $manager->ban();
        return back()->with('success', 'Manager banned successfully.');
    }

    public function unban(Staff $manager)
    {
        $manager->unban();
        return back()->with('success', 'Manager unbanned successfully.');
    }
}