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


        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('adoption', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'))
                    ->orWhereHas('adopter', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        $sortDir = $request->filled('sort_dir') ? $request->sort_dir : 'desc';
        $query->orderBy('created_at', $sortDir);

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
        $request = AdoptionRequest::findOrFail($id);

        $user = Auth::user();

        if ($user->role === 'shelter' && $request->adoption->shelter_id !== $user->id) {
            return redirect()->back()->with('error', 'You are not authorized to delete this request.');
        }

        $request->delete();

        return redirect()->back()->with('success', 'Adoption request deleted successfully.');
    }

    public function approve($id)
    {
        $request = AdoptionRequest::findOrFail($id);

        if ($request->status === 'pending') {
            $request->update(['status' => 'approved']);
            AdoptionRequest::where('adoption_id', $request->adoption_id)
                ->where('id', '!=', $request->id)
                ->update(['status' => 'rejected']);
        }

        return redirect()->back()->with('success', 'Adoption request approved.');
    }

    public function reject($id)
    {
        $request = AdoptionRequest::findOrFail($id);

        if ($request->status === 'pending') {
            $request->update(['status' => 'rejected']);
        }

        return redirect()->back()->with('success', 'Adoption request rejected.');
    }
    public function history(Request $request)
    {
        $shelterId = Auth::id();

        $requests = AdoptionRequest::with(['adopter', 'adoption'])
            ->whereHas('adoption', fn($q) => $q->where('shelter_id', $shelterId))

            // Filter by status if provided
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })

            // Filter by search across pet name, species, or adopter name
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('adoption', function ($q2) use ($request) {
                    $q2->where('name', 'like', "%{$request->search}%")
                        ->orWhere('species', 'like', "%{$request->search}%");
                })
                    ->orWhereHas('adopter', fn($q3) => $q3->where('name', 'like', "%{$request->search}%"));
            })

            // Sort by updated date
            ->orderBy('updated_at', $request->sort_dir ?? 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.shelter.adoption-requests.history', compact('requests'));
    }
}
