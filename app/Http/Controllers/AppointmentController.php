<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['pet', 'owner', 'vet']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('pet', fn($q2) => $q2->where('name', 'like', "%$search%")
                    ->orWhere('species', 'like', "%$search%"))
                    ->orWhereHas('owner', fn($q2) => $q2->where('name', 'like', "%$search%"))
                    ->orWhereHas('vet', fn($q2) => $q2->where('name', 'like', "%$search%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appts = $query->orderBy('appt_date', 'desc')->paginate(10)->withQueryString();

        return view('dashboard.admin.appointments.list', compact('appts'));
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
        $appt = Appointment::with(['pet', 'owner', 'vet'])->findOrFail($id);

        $pets = Pet::with('owner')->orderBy('name')->get();

        $owners = User::where('role', 'owner')->orderBy('name')->get();

        $vets = User::where('role', 'vet')->orderBy('name')->get();


        return view('dashboard.admin.appointments.edit', compact('appt', 'pets', 'owners', 'vets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'owner_id' => 'required|exists:users,id',
            'vet_id' => 'required|exists:users,id',
            'appt_date' => 'required|date|after:today',
            'appt_time' => 'required',
            'status' => 'required|in:pending,approved,completed',
        ]);

        $appt = Appointment::findOrFail($id);
        $appt->update($request->all());

        return redirect()->route('appts.index')->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();
        return redirect()->route('appts.index')->with('success', 'Appointment deleted successfully.');
    }

    public function vetSlots(User $vet)
    {

        $slots = json_decode($vet->vet->available_slots ?? '[]', true);

        return response()->json($slots);
    }
}
