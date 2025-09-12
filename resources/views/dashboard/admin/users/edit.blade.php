@extends('dashboard.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .card-modern {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            background: #fff;
        }
        .card-header-modern {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
        }
        .form-label {
            font-weight: 500;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #d1d3e0;
            padding: 0.55rem 0.75rem;
        }
        .btn-modern {
            border-radius: 10px;
            font-weight: 500;
        }
        .slot-row .form-select, .slot-row .form-control {
            border-radius: 8px;
        }
        .slot-row .btn {
            border-radius: 0 8px 8px 0;
        }
    </style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-modern">
                <div class="card-header-modern">
                    <span>Edit User: {{ $user->name }}</span>
                    <a href="{{ route('users.index') }}" class="btn btn-modern btn-outline-light">Go Back</a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Personal Info -->
                        <h5 class="mb-3"><i class="bx bx-id-card me-1"></i> Personal Info</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="contact" class="form-control" value="{{ $user->contact }}">
                                @error('contact') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ $user->address }}">
                                @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        @if($user->role == 'vet' && $user->vet)
                        <!-- Vet Details -->
                        <h5 class="mb-3"><i class="bx bx-hospital me-1"></i> Vet Details</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Specialization</label>
                                <input type="text" name="specialization" class="form-control" value="{{ $user->vet->specialization ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Experience (yrs)</label>
                                <input type="number" name="experience" class="form-control" value="{{ $user->vet->experience ?? '' }}">
                            </div>
                        </div>

                        <!-- Available Slots -->
                        <h5 class="mb-3"><i class="bx bx-time-five me-1"></i> Available Slots</h5>
                        <div id="slots-container">
                            @php $slots = json_decode($user->vet->available_slots ?? '[]', true); @endphp
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
                                        @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
                                            <option value="{{ $d }}" {{ $d==$day?'selected':'' }}>{{ $d }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="slot_start_time[]" class="form-control slot-picker" value="{{ $start }}">
                                    <input type="text" name="slot_end_time[]" class="form-control slot-picker" value="{{ $end }}">
                                    <button type="button" class="btn btn-outline-danger remove-slot"><i class="bx bx-x"></i></button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-slot">
                            <i class="bx bx-plus"></i> Add Slot
                        </button>
                        @endif

                        @if($user->role == 'shelter' && $user->shelter)
                        <h5 class="mb-3"><i class="bx bx-building-house me-1"></i> Shelter Details</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Shelter Name</label>
                                <input type="text" name="shelter_name" class="form-control" value="{{ $user->shelter->shelter_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" value="{{ $user->shelter->contact_person }}">
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-modern">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-modern">Update User</button>
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
            flatpickr(div.querySelectorAll(".slot-picker"), { enableTime:true, noCalendar:true, dateFormat:"H:i" });
        });

        document.addEventListener('click', e => {
            if(e.target.closest('.remove-slot')){
                e.target.closest('.slot-row').remove();
            }
        });
    </script>
@endpush
