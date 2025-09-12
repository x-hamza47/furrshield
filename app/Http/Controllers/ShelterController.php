<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Models\User;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'shelter')->with('shelter');

        if ($request->filled('shelter_name')) {
            $query->whereRelation('shelter', 'shelter_name', 'like', '%' . $request->shelter_name . '%');
        }

        if ($request->filled('contact_person')) {
            $query->whereRelation('shelter', 'contact_person', 'like', '%' . $request->contact_person . '%');
        }

        if ($request->filled('address')) {
            $query->where('address', 'like', '%' . $request->address . '%'); // users table
        }

        $shelters = $query->orderBy('id', 'desc')->paginate(9)->withQueryString();

        return view('dashboard.admin.shelters.list', compact('shelters'));
    }

    public function edit(string $id)
    {
        $shelter = User::findOrFail($id);
        $shelter->load('shelter');
        return view('dashboard.admin.shelters.edit', compact('shelter'));
    }
}
