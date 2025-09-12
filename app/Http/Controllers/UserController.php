<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query()->where('role', '!=', 'admin');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('dashboard.admin.users.list', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $user = User::findOrFail($id);

        if ($user->role == 'vet') {
            $user->load('vet');
        } else if ($user->role == 'shelter') {
            $user->load('shelter');
        }

        return view('dashboard.admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ];


        if ($user->role == 'vet') {
            $rules['specialization'] = 'nullable|string';
            $rules['experience'] = 'nullable|integer|min:0';
            $rules['available_slots.*'] = 'nullable|string';
        }

        if ($user->role == 'shelter') {
            $rules['shelter_name'] = 'nullable|string';
            $rules['contact_person'] = 'nullable|string';
            $rules['shelter_address'] = 'nullable|string';
        }

        $request->validate($rules);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'address' => $request->address,
        ]);


        if ($user->role == 'vet' && $user->vet) {
            $slots = [];
            if ($request->filled('slot_day')) {
                foreach ($request->slot_day as $index => $day) {
                    $start = $request->slot_start_time[$index] ?? '';
                    $end = $request->slot_end_time[$index] ?? '';
                    if ($day && $start && $end) {
                        $slots[] = "$day $start-$end";
                    }
                }
            }

            $user->vet->update([
                'specialization' => $request->specialization,
                'experience' => $request->experience,
                'available_slots' => json_encode($slots),
            ]);
        }

        if ($user->role == 'shelter' && $user->shelter) {
            $user->shelter->update([
                'shelter_name' => $request->shelter_name,
                'contact_person' => $request->contact_person,
            ]);
        }

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back()->with('success', 'User Deleted successfully.');
    }
}
