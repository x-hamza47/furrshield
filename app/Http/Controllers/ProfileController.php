<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Models\User;
use App\Models\Vet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $vet = null;
        $shelter = null;

        if ($user->role === 'vet') {
            $vet = Vet::where('vet_id', $user->id)->first();
        } elseif ($user->role === 'shelter') {
            $shelter = Shelter::where('shelter_id', $user->id)->first();
        }

        return view('dashboard.profile', compact('user', 'vet', 'shelter'));
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'contact' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];

        if ($user->role === 'vet') {
            $rules['specialization']  = 'nullable|string|max:255';
            $rules['experience']      = 'nullable|integer|min:0';
            $rules['slot_day']        = 'nullable|array';
            $rules['slot_day.*']      = 'nullable|string|in:Mon,Tue,Wed,Thu,Fri,Sat,Sun';
            $rules['slot_start_time'] = 'nullable|array';
            $rules['slot_start_time.*'] = 'nullable|string|max:10';
            $rules['slot_end_time']   = 'nullable|array';
            $rules['slot_end_time.*'] = 'nullable|string|max:10';
        }

        if ($user->role === 'shelter') {
            $rules['shelter_name']   = 'required|string|max:255';
            $rules['contact_person'] = 'nullable|string|max:255';
            $rules['description']    = 'nullable|string';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && $user->profile_picture !== 'default.png') {
                $oldPath = public_path('storage/user_profiles/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $fileName = uniqid('profile_') . '.' . $request->file('profile_picture')->extension();
            $request->file('profile_picture')->storeAs('user_profiles', $fileName, 'public');

            $validated['profile_picture'] = $fileName;
        }

        $user->update([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'contact' => $validated['contact'] ?? null,
            'address' => $validated['address'] ?? null,
            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,
        ]);


        if ($user->role === 'vet') {
            $days   = $request->input('slot_day', []);
            $starts = $request->input('slot_start_time', []);
            $ends   = $request->input('slot_end_time', []);

            $slots = [];
            for ($i = 0; $i < count($days); $i++) {
                if (!empty($days[$i]) && !empty($starts[$i]) && !empty($ends[$i])) {
                    $slots[] = "{$days[$i]} {$starts[$i]}-{$ends[$i]}";
                }
            }

            $user->vet()->updateOrCreate(
                ['vet_id' => $user->id],
                [
                    'specialization'  => $validated['specialization'] ?? null,
                    'experience'      => $validated['experience'] ?? null,
                    'available_slots' => json_encode($slots),
                ]
            );
        }

        if ($user->role === 'shelter') {
            $user->shelter()->updateOrCreate(
                ['shelter_id' => $user->id],
                [
                    'shelter_name'   => $validated['shelter_name'],
                    'contact_person' => $validated['contact_person'] ?? null,
                    'description'    => $validated['description'] ?? null,
                ]
            );
        }

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully!');
    }

    public function uploadAvatar(Request $request, $id)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png|max:2048',
        ]);

        $user = User::findOrFail($id);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                $oldPath = public_path('storage/user_profiles/' . $user->profile_picture);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $filename = uniqid('profile_') . '.' . $request->file('profile_picture')->getClientOriginalExtension();

            $request->file('profile_picture')->storeAs('user_profiles', $filename, 'public');
            $user->profile_picture = $filename;
            $user->save();
        }

        return back()->with('success', 'Profile picture updated.');
    }

    public function removeAvatar($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_picture) {
            $filePath = public_path('storage/user_profiles/' . $user->profile_picture);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $user->profile_picture = null;
            $user->save();
        }

        return back()->with('success', 'Profile picture removed successfully.');
    }

    public function updateAvatar(Request $request, $id)
    {
        $request->validate([
            'profile_picture' => 'image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $user = User::findOrFail($id);

        // If old pic is NOT default, delete it
        if ($user->profile_picture !== 'user_pictures/default.png') {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new one
        $path = $request->file('profile_picture')->store('user_pictures', 'public');

        $user->profile_picture = $path;
        $user->save();

        return back()->with('success', 'Profile picture updated.');
    }





    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (Auth::id() !== (int) $id) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->profile_picture && $user->profile_picture !== 'default.png') {
            $filePath = public_path('storage/user_profiles/' . $user->profile_picture);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        Auth::logout();

        $user->delete();

        return redirect('/')->with('success', 'Your account has been deleted successfully.');
    }
}
