<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HealthRecordController extends Controller
{
    public function index(Request $request)
    {
        // Subquery: get the latest health record ID per pet
        $latestIds = HealthRecord::selectRaw('MAX(id) as id')
            ->groupBy('pet_id');

        // Query only the latest records
        $query = HealthRecord::with(['pet.owner', 'vet'])
            ->whereIn('id', $latestIds);

        // 🔍 Search filter
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'pet',
                    fn($q2) =>
                    $q2->where('name', 'like', "%$search%")
                        ->orWhere('species', 'like', "%$search%") // ✅ species from pets
                )
                    ->orWhereHas(
                        'pet.owner',
                        fn($q2) =>
                        $q2->where('name', 'like', "%$search%")
                    )
                    ->orWhereHas(
                        'vet',
                        fn($q2) =>
                        $q2->where('name', 'like', "%$search%")
                    )
                    ->orWhere('diagnosis', 'like', "%$search%")
                    ->orWhere('treatment', 'like', "%$search%");
            });
        }


        // 📅 Visit date filter (only applies to latest records)
        if ($request->filled('visit_date')) {
            $query->whereDate('visit_date', $request->visit_date);
        }

        // 🐾 Paginate latest records (unique per pet)
        $records = $query
            ->with(['pet.healthRecords' => function ($q) {
                $q->orderByDesc('visit_date'); // preload full history for modal
            }])
            ->latest('visit_date')
            ->paginate(10);

        return view('dashboard.vet.health-record.list', compact('records'));
    }


    public function edit(string $id)
    {
        $record = HealthRecord::with(['pet.owner', 'vet'])->findOrFail($id);

        // 🐾 Fetch all health records for this pet, newest first
        $history = HealthRecord::with('vet')
            ->where('pet_id', $record->pet_id)
            ->orderByDesc('visit_date')
            ->get();

        return view('dashboard.vet.health-record.edit', compact('record', 'history'));
    }

    public function update(Request $request, $id)
    {
        $record = HealthRecord::findOrFail($id);

        $data = $request->validate([
            'symptoms'   => 'nullable|string',
            'diagnosis'  => 'nullable|string',
            'treatment'  => 'nullable|string',
            'notes'      => 'nullable|string',
            'lab_tests'  => 'array',
            'lab_results' => 'array',
        ]);

        // Merge tests + results into associative array
        $labReports = [];
        if ($request->filled('lab_tests')) {
            foreach ($request->lab_tests as $index => $test) {
                if (!empty($test) && !empty($request->lab_results[$index])) {
                    $labReports[$test] = $request->lab_results[$index];
                }
            }
        }

        $record->update([
            'symptoms'   => $data['symptoms'] ?? null,
            'diagnosis'  => $data['diagnosis'] ?? null,
            'treatment'  => $data['treatment'] ?? null,
            'notes'      => $data['notes'] ?? null,
            'lab_reports' => $labReports,
        ]);

        return redirect()->route('health-records.index')->with('success', 'Health record updated successfully!');
    }


    public function destroy(string $id)
    {
        $record = HealthRecord::findOrFail($id);
        $record->delete();
        return redirect()
            ->route('health-records.index')
            ->with('success', 'All health records for this pet have been deleted.');
    }

    public function petDestroy(string $id)
    {
        // Find the health record and get its pet_id
        $record = HealthRecord::findOrFail($id);
        $petId = $record->pet_id;

        // Delete all health records for this pet
        HealthRecord::where('pet_id', $petId)->delete();

        // ✅ Correct way to attach flash message
        return redirect()
            ->route('health-records.index')
            ->with('success', 'All health records for this pet have been deleted.');
    }
}
