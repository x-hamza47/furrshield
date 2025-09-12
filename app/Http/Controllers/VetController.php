<?php

namespace App\Http\Controllers;

use App\Models\Vet;
use App\Models\User;
use Illuminate\Http\Request;

class VetController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'vet')->with('vet');


        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }


        if ($request->filled('specialization')) {
            $query->whereHas('vet', function ($q) use ($request) {
                $q->where('specialization', $request->specialization);
            });
        }

        if ($request->filled('day')) {
            $day = $request->day; 
            $query->whereHas('vet', function ($q) use ($day) {
                $q->where(function ($q2) use ($day) {
                    $q2->whereRaw('JSON_CONTAINS(available_slots, ?)', [json_encode($day . '%')])
                        ->orWhere('available_slots', 'like', "%$day%");
                });
            });
        }
        $specializations = Vet::distinct()->pluck('specialization')->filter()->toArray();

        $vets = $query->orderBy('name')->paginate(9)->withQueryString();

        return view('dashboard.admin.vets.list', compact('vets', 'specializations'));
    }

    public function edit(string $id)
    {
        $vet = User::findOrFail($id);
        $vet->load('vet');
        return view('dashboard.admin.vets.edit', compact('vet'));
    }
}
