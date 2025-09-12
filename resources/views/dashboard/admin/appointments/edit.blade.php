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

                            <div class="mb-3">
                                <label class="form-label">Pet</label>
                                <select name="pet_id" class="form-select">
                                    @foreach ($pets as $pet)
                                        <option value="{{ $pet->id }}"
                                            {{ $appt->pet_id == $pet->id ? 'selected' : '' }}>
                                            {{ $pet->name }} ({{ $pet->owner->name ?? 'No Owner' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('pet_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Owner</label>
                                <select name="owner_id" class="form-select">
                                    @foreach ($owners as $owner)
                                        <option value="{{ $owner->id }}"
                                            {{ $appt->owner_id == $owner->id ? 'selected' : '' }}>
                                            {{ $owner->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('owner_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vet</label>
                                <select name="vet_id" class="form-select" id="vet-select">
                                    @foreach ($vets as $vet)
                                        <option value="{{ $vet->id }}"
                                            {{ $appt->vet_id == $vet->id ? 'selected' : '' }}>
                                            {{ $vet->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3" id="slots-container">
                                <label class="form-label">Available Slots</label>
                                <select name="appt_time" class="form-select">
                                    <option value="{{ $appt->appt_time }}">{{ $appt->appt_time }} (current)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="appt_date" class="form-control"
                                    min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                    value="{{ $appt->appt_date }}">
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    @foreach (['pending', 'approved', 'completed'] as $status)
                                        <option value="{{ $status }}"
                                            {{ $appt->status == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Actions -->
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
    @push('scripts')
        <script>
            document.getElementById('vet-select').addEventListener('change', function() {
                const vetId = this.value;
                const slotsContainer = document.getElementById('slots-container');
                const select = slotsContainer.querySelector('select');

                // Clear old options
                select.innerHTML = '<option value="">Select Time</option>';

                if (!vetId) return;

                fetch(`/vet-slots/${vetId}`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(slot => {
                            // slot example: "Mon 10:00-14:00"
                            const parts = slot.split(' ');
                            const times = parts[1].split('-');
                            const start = times[0];
                            const end = times[1];

                            const option = document.createElement('option');
                            option.value = start; // you can store start time
                            option.textContent = `${parts[0]} ${start} - ${end}`;
                            select.appendChild(option);
                        });
                    });
            });
        </script>
    @endpush
@endpush
