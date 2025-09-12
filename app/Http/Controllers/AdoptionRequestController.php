<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionRequest;
use Illuminate\Support\Facades\Auth;

class AdoptionRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AdoptionRequest::with(['adoption', 'adopter'])
            ->where('status', 'pending')
            ->whereHas('adoption', fn($q) => $q->where('shelter_id', Auth::id()));

        // Search by Pet name or Adopter name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('adoption', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'))
                    ->orWhereHas('adopter', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        // Sort
        $sortBy = $request->filled('sort_by') ? $request->sort_by : 'created_at';
        $sortDir = $request->filled('sort_dir') ? $request->sort_dir : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $requests = $query->paginate(10)->withQueryString(); 

        return view('dashboard.shelter.adoption-requests.list', compact('requests'));
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
