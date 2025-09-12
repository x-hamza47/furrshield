@extends('dashboard.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h3 class="mb-0 text-white"><i class="bx bx-user-edit me-2"></i>Edit Vet: {{ $vet->name }}</h3>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ route('users.update', $vet->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Personal Info -->
                            <h5 class="mb-3"><i class="bx bx-id-card me-1"></i> Personal Info</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $vet->name }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $vet->email }}">
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="contact" class="form-control" value="{{ $vet->contact }}">
                                    @error('contact')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control" value="{{ $vet->address }}">
                                    @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vet Details -->
                            <h5 class="mb-3"><i class="bx bx-hospital me-1"></i> Vet Details</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Specialization</label>
                                    <input type="text" name="specialization" class="form-control"
                                        value="{{ $vet->vet->specialization ?? '' }}">
                                    @error('specialization')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Experience (yrs)</label>
                                    <input type="number" name="experience" class="form-control"
                                        value="{{ $vet->vet->experience ?? '' }}">
                                    @error('experience')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Available Slots -->
                            <h5 class="mb-3"><i class="bx bx-time-five me-1"></i> Available Slots</h5>
                            <div id="slots-container">
                                @php $slots = json_decode($vet->vet->available_slots ?? '[]', true); @endphp
                                @foreach ($slots as $slot)
                                    @php
                                        $parts = explode(' ', $slot); 
                                        $day = $parts[0] ?? '';
                                        $times = explode('-', $parts[1] ?? '');
                                        $start = $times[0] ?? '';
                                        $end = $times[1] ?? '';
                                    @endphp
                                    <div class="input-group mb-2 slot-row">
                                        <select name="slot_day[]" class="form-select me-2">
                                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $d)
                                                <option value="{{ $d }}" {{ $d == $day ? 'selected' : '' }}>
                                                    {{ $d }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="slot_start_time[]" class="form-control slot-picker"
                                            value="{{ $start }}">
                                        <input type="text" name="slot_end_time[]" class="form-control slot-picker"
                                            value="{{ $end }}">
                                        <button type="button" class="btn btn-outline-danger remove-slot"><i
                                                class="bx bx-x"></i></button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-slot">
                                <i class="bx bx-plus"></i> Add Slot
                            </button>

                            <!-- Footer -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('vets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Vet</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>

        flatpickr(".slot-picker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });

        document.getElementById('add-slot').addEventListener('click', () => {
            const container = document.getElementById('slots-container');
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2', 'slot-row');
            div.innerHTML = `
        <select name="slot_day[]" class="form-select me-2">
            <option value="Mon">Mon</option>
            <option value="Tue">Tue</option>
            <option value="Wed">Wed</option>
            <option value="Thu">Thu</option>
            <option value="Fri">Fri</option>
            <option value="Sat">Sat</option>
            <option value="Sun">Sun</option>
        </select>
        <input type="text" name="slot_start_time[]" class="form-control slot-picker" placeholder="10:00">
        <input type="text" name="slot_end_time[]" class="form-control slot-picker" placeholder="14:00">
        <button type="button" class="btn btn-outline-danger remove-slot"><i class="bx bx-x"></i></button>
    `;
            container.appendChild(div);
        });

        // Remove slot row
        document.addEventListener('click', e => {
            if (e.target.closest('.remove-slot')) {
                e.target.closest('.slot-row').remove();
            }
        });
    </script>


    {{-- <script>
        const container = document.getElementById('slots-container');
        document.getElementById('add-slot').addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `
            <span class="input-group-text"><i class="bx bx-time"></i></span>
            <input type="text" name="available_slots[]" class="form-control" placeholder="Mon 10:00-14:00">
            <button type="button" class="btn btn-outline-danger remove-slot"><i class="bx bx-x"></i></button>
        `;
            container.appendChild(div);
        });

        document.addEventListener('click', e => {
            if (e.target.closest('.remove-slot')) {
                e.target.closest('.input-group').remove();
            }
        });
    </script> --}}
@endpush
