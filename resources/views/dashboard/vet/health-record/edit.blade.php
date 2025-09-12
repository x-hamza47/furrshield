@extends('dashboard.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header bg-success text-white rounded-top-4">
                        <h3 class="mb-0 text-white">
                            <i class="bx bx-edit-alt me-2"></i>Edit Health Record
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        {{-- Pet & Owner Info --}}
                        <div class="mb-3">
                            <label class="form-label">Pet</label>
                            <input type="text" class="form-control" value="{{ $record->pet->name }} ({{ $record->pet->species }})" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <input type="text" class="form-control" value="{{ $record->pet->owner->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Vet</label>
                            <input type="text" class="form-control" value="{{ $record->vet->name ?? 'N/A' }}" readonly>
                        </div>

                        {{-- Edit Form --}}
                        <form action="{{ route('health-records.update', $record->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Symptoms</label>
                                <textarea name="symptoms" class="form-control @error('symptoms') is-invalid @enderror" rows="2">{{ old('symptoms', $record->symptoms) }}</textarea>
                                @error('symptoms')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Diagnosis</label>
                                <textarea name="diagnosis" class="form-control @error('diagnosis') is-invalid @enderror" rows="3">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                                @error('diagnosis')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Treatment</label>
                                <textarea name="treatment" class="form-control @error('treatment') is-invalid @enderror" rows="3">{{ old('treatment', $record->treatment) }}</textarea>
                                @error('treatment')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $record->notes) }}</textarea>
                                @error('notes')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Dynamic Lab Reports --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Lab Reports</label>
                                <div id="lab-reports-wrapper">
                                    @php
                                        $existingReports = old('lab_reports', $record->lab_reports ?? []);
                                    @endphp

                                    @if(!empty($existingReports))
                                        @foreach($existingReports as $test => $result)
                                            <div class="d-flex gap-2 mb-2 lab-report-pair">
                                                <input type="text" name="lab_tests[]" class="form-control" placeholder="Test Name" value="{{ $test }}">
                                                <input type="text" name="lab_results[]" class="form-control" placeholder="Result" value="{{ $result }}">
                                                <button type="button" class="btn btn-danger btn-sm remove-pair">X</button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="d-flex gap-2 mb-2 lab-report-pair">
                                            <input type="text" name="lab_tests[]" class="form-control" placeholder="Test Name">
                                            <input type="text" name="lab_results[]" class="form-control" placeholder="Result">
                                            <button type="button" class="btn btn-danger btn-sm remove-pair">X</button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" id="add-lab-report">
                                    + Add Test/Result
                                </button>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('health-records.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-success">Update Record</button>
                            </div>
                        </form>

                        {{-- History Section --}}
                        <hr class="my-4">
                        <h5 class="fw-bold">Past Health Records</h5>
                        <div style="max-height: 300px; overflow-y:auto;">
                            @foreach ($history as $h)
                                <div class="mb-3 pb-2 border-bottom">
                                    <p><strong>Date:</strong>
                                        {{ $h->visit_date ? \Carbon\Carbon::parse($h->visit_date)->format('d M, Y') : '-' }}
                                    </p>
                                    <p><strong>Symptoms:</strong> {{ $h->symptoms ?? 'N/A' }}</p>
                                    <p><strong>Diagnosis:</strong> {{ $h->diagnosis ?? 'N/A' }}</p>
                                    <p><strong>Treatment:</strong> {{ $h->treatment ?? 'N/A' }}</p>
                                    <p><strong>Notes:</strong> {{ $h->notes ?? 'N/A' }}</p>
                                    @if (!empty($h->lab_reports))
                                        <p><strong>Lab Reports:</strong></p>
                                        <ul>
                                            @foreach ($h->lab_reports as $test => $result)
                                                <li><strong>{{ ucfirst($test) }}:</strong> {{ $result }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <p><strong>Vet:</strong> {{ $h->vet->name ?? 'N/A' }}</p>
                                    <a href="{{ route('health-records.edit', $h->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bx bx-edit-alt"></i> Edit
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById("lab-reports-wrapper");
        const addBtn = document.getElementById("add-lab-report");

        addBtn.addEventListener("click", function () {
            const div = document.createElement("div");
            div.classList.add("d-flex", "gap-2", "mb-2", "lab-report-pair");
            div.innerHTML = `
                <input type="text" name="lab_tests[]" class="form-control" placeholder="Test Name">
                <input type="text" name="lab_results[]" class="form-control" placeholder="Result">
                <button type="button" class="btn btn-danger btn-sm remove-pair">X</button>
            `;
            wrapper.appendChild(div);
        });

        wrapper.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-pair")) {
                e.target.parentElement.remove();
            }
        });
    });
</script>
@endpush