@extends('dashboard.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h3 class="mb-0 text-white"><i class="bx bx-edit-alt me-2"></i>Edit Appointment</h3>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('appts.update', $appt->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Vet (readonly) --}}
                            <div class="mb-3">
                                <label class="form-label">Vet</label>
                                <input type="text" class="form-control" value="{{ $appt->vet->name }}" readonly>
                                <input type="hidden" name="vet_id" id="vet-id" value="{{ $appt->vet_id }}">
                            </div>

                            {{-- Owner (readonly) --}}
                            <div class="mb-3">
                                <label class="form-label">Owner</label>
                                <input type="text" class="form-control" value="{{ $appt->owner->name }}" readonly>
                                <input type="hidden" name="owner_id" value="{{ $appt->owner_id }}">
                            </div>

                            {{-- Pet (readonly) --}}
                            <div class="mb-3">
                                <label class="form-label">Pet</label>
                                <input type="text" class="form-control" value="{{ $appt->pet->name }}" readonly>
                                <input type="hidden" name="pet_id" value="{{ $appt->pet_id }}">
                            </div>

                            {{-- Appointment Time (select from vet slots) --}}
                            <div class="mb-3" id="slots-container">
                                <label class="form-label">Appointment Time</label>
                                <select name="appt_time" id="appt-time-select" class="form-select" required>
                                    <option value="{{ $appt->appt_time }}" selected>{{ $appt->appt_time }} (current)
                                    </option>
                                </select>
                                @error('appt_time')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Date (editable) --}}
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="appt_date" class="form-control"
                                    min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" value="{{ $appt->appt_date }}"
                                    required>
                                @error('appt_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="status"
                                    class="form-select status-selector @error('status') is-invalid @enderror"
                                    data-id="{{ $appt->id }}" required>
                                    @foreach (['pending', 'rejected', 'rescheduled', 'completed'] as $status)
                                        <option value="{{ $status }}"
                                            {{ old('status', $appt->status) === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- @error('status') --}}
                                {{-- @enderror --}}
                            </div>
                            {{-- <small >Yahan likh</small> --}}



                            {{-- Health Record Fields --}}
                            <div class="health-fields d-none" id="health-fields-{{ $appt->id }}">
                                <div class="mb-3">
                                    <label class="form-label">Symptoms</label>
                                    <textarea name="symptoms" class="form-control">{{ old('symptoms') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Diagnosis</label>
                                    <textarea name="diagnosis" class="form-control">{{ old('diagnosis') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Treatment</label>
                                    <textarea name="treatment" class="form-control">{{ old('treatment') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lab Reports</label>
                                    <small class="text-muted d-block mb-2">Add test name and result. You can add multiple
                                        rows.</small>

                                    <div id="lab-report-container">
                                        <!-- Default pair -->
                                        <div class="lab-report-row d-flex gap-2 mb-2">
                                            <input type="text" class="form-control" placeholder="Test Name (e.g., BP)"
                                                name="lab_test_name[]">
                                            <input type="text" class="form-control" placeholder="Result (e.g., 120)"
                                                name="lab_test_result[]">
                                            <button type="button" class="btn btn-danger remove-row">&times;</button>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-lab-report">
                                        + Add Test / Result
                                    </button>

                                    {{-- Hidden field to store JSON --}}
                                    <input type="hidden" name="lab_reports" id="lab_reports">
                                    @error('lab_reports')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>



                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('appts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const vetId = document.getElementById('vet-id').value;
            const select = document.getElementById('appt-time-select');

            // Clear all but the current option
            select.innerHTML =
                `<option value="{{ $appt->appt_time }}" selected>{{ $appt->appt_time }} (current)</option>`;

            fetch(`/vet-slots/${vetId}`)
                .then(res => res.json())
                .then(data => {
                    // Append slots except current (avoid duplicate)
                    data.forEach(slot => {
                        if (slot !== "{{ $appt->appt_time }}") {
                            const parts = slot.split(' ');
                            const times = parts[1].split('-');
                            const start = times[0];
                            const end = times[1];

                            const option = document.createElement('option');
                            option.value = start;
                            option.textContent = `${parts[0]} ${start} - ${end}`;
                            select.appendChild(option);
                        }
                    });
                })
                .catch(err => {
                    console.error('Error loading vet slots:', err);
                });
        });
    </script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.querySelector('.status-selector');
            const healthFields = document.getElementById('health-fields-{{ $appt->id }}');

            function toggleHealthFields() {
                if (statusSelect.value === 'completed') {
                    healthFields.classList.remove('d-none');
                } else {
                    healthFields.classList.add('d-none');
                }
            }

            // Run on load (in case status is already completed)
            toggleHealthFields();

            // Run when status changes
            statusSelect.addEventListener('change', toggleHealthFields);
        });
    </script>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById("lab-report-container");
            const addBtn = document.getElementById("add-lab-report");
            const form = document.querySelector("form");
            const hiddenInput = document.getElementById("lab_reports");

            // Add new row
            addBtn.addEventListener("click", function() {
                const row = document.createElement("div");
                row.classList.add("lab-report-row", "d-flex", "gap-2", "mb-2");
                row.innerHTML = `
            <input type="text" class="form-control" placeholder="Test Name (e.g., BP)" name="lab_test_name[]">
            <input type="text" class="form-control" placeholder="Result (e.g., 120)" name="lab_test_result[]">
            <button type="button" class="btn btn-danger remove-row">&times;</button>
        `;
                container.appendChild(row);
            });

            // Remove row
            container.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-row")) {
                    e.target.closest(".lab-report-row").remove();
                }
            });

            // On submit → convert pairs to JSON
            form.addEventListener("submit", function() {
                const names = document.querySelectorAll("[name='lab_test_name[]']");
                const results = document.querySelectorAll("[name='lab_test_result[]']");
                let data = {};

                names.forEach((input, i) => {
                    const name = input.value.trim();
                    const result = results[i].value.trim();
                    if (name && result) {
                        data[name] = result;
                    }
                });

                hiddenInput.value = JSON.stringify(data);
            });
        });
    </script>
@endpush