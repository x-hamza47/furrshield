@extends('dashboard.main')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .card-modern {
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
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

        .slot-row .form-select,
        .slot-row .form-control {
            border-radius: 8px;
        }

        .slot-row .btn {
            border-radius: 0 8px 8px 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Account Settings /</span> Account
        </h4>

        <div class="row">
            <div class="col-md-12">
                <div class="card-modern mb-4">
                    <div class="card-header-modern">
                        <span>Profile Details</span>
                    </div>

                    <!-- Profile Picture -->
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            <img src="{{ $user->profile_picture
                                ? asset('storage/user_profiles/' . $user->profile_picture)
                                : asset('dashboard/assets/img/avatars/profile-dummy.jpg') }}"
                                alt="user-avatar" class="d-block rounded" height="100" width="100"
                                id="uploadedAvatar" />

                            <div class="button-wrapper">
                                <form action="{{ route('profile.uploadAvatar', $user->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <label for="upload" class="btn btn-primary me-2 mb-2 btn-modern" tabindex="0">
                                        <span class="d-none d-sm-block">Upload new photo</span>
                                        <i class="bx bx-upload d-block d-sm-none"></i>
                                        <input type="file" name="profile_picture" id="upload"
                                            class="account-file-input" hidden accept="image/png, image/jpeg" />
                                    </label>
                                    <button type="submit" class="btn btn-success mb-2 btn-modern">Save</button>
                                </form>

                                <form action="{{ route('profile.removeAvatar', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-secondary btn-modern account-image-reset mb-2">
                                        <i class="bx bx-reset d-block d-sm-none"></i>
                                        <span class="d-none d-sm-block">Remove Profile Picture</span>
                                    </button>
                                </form>
                                <p class="text-muted mb-0">Allowed JPG or PNG. Max size of 2MB</p>
                            </div>
                        </div>
                        <hr class="my-0" />

                        <!-- Profile Update Form -->
                        <div class="card-body">
                            <form action="{{ route('profile.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Name</label>
                                        <input class="form-control" type="text" name="name"
                                            value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-mail</label>
                                        <input class="form-control" type="email" name="email"
                                            value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Contact</label>
                                        <input class="form-control" type="text" name="contact"
                                            value="{{ old('contact', $user->contact) }}">
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Address</label>
                                        <input class="form-control" type="text" name="address"
                                            value="{{ old('address', $user->address) }}">
                                    </div>

                                    @if ($user->role === 'vet')
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Specialization</label>
                                            <input class="form-control" type="text" name="specialization"
                                                value="{{ old('specialization', $vet->specialization ?? '') }}">
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Experience (years)</label>
                                            <input class="form-control" type="number" name="experience"
                                                value="{{ old('experience', $vet->experience ?? '') }}">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Available Slots</label>
                                            <div id="slots-container">
                                                @php
                                                    $slots = old(
                                                        'available_slots',
                                                        $vet?->available_slots
                                                            ? json_decode($vet->available_slots, true)
                                                            : [],
                                                    );
                                                @endphp
                                                @foreach ($slots as $slot)
                                                    @php
                                                        $day = explode(' ', $slot)[0] ?? '';
                                                        $time = explode(' ', $slot)[1] ?? '';
                                                        [$start, $end] = explode('-', $time . '-');
                                                    @endphp
                                                    <div class="input-group mb-2 slot-row">
                                                        <select name="slot_day[]" class="form-select me-2">
                                                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $d)
                                                                <option value="{{ $d }}"
                                                                    {{ $d == $day ? 'selected' : '' }}>{{ $d }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input type="text" name="slot_start_time[]"
                                                            class="form-control slot-picker" placeholder="Start Time"
                                                            value="{{ $start }}">
                                                        <input type="text" name="slot_end_time[]"
                                                            class="form-control slot-picker" placeholder="End Time"
                                                            value="{{ $end }}">
                                                        <button type="button"
                                                            class="btn btn-outline-danger remove-slot"><i
                                                                class="bx bx-x"></i></button>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm mt-2 btn-modern"
                                                id="add-slot">
                                                <i class="bx bx-plus"></i> Add Slot
                                            </button>
                                        </div>
                                    @elseif ($user->role === 'shelter')
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Shelter Name</label>
                                            <input class="form-control" type="text" name="shelter_name"
                                                value="{{ old('shelter_name', $shelter->shelter_name ?? '') }}" required>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Contact Person</label>
                                            <input class="form-control" type="text" name="contact_person"
                                                value="{{ old('contact_person', $shelter->contact_person ?? '') }}">
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control" name="description" rows="3">{{ old('description', $shelter->description ?? '') }}</textarea>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-modern me-2">Save changes</button>
                                    <button type="reset" class="btn btn-outline-secondary btn-modern">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="card-modern mt-5">
                        <div class="card-header-modern">
                            <span>Delete Account</span>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                                <p class="mb-0">Once you delete your account, there is no going back. Please be certain.
                                </p>
                            </div>
                            <form action="{{ route('profile.destroy', $user->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="confirm"
                                        id="accountActivation" required>
                                    <label class="form-check-label" for="accountActivation">I confirm my account
                                        deactivation</label>
                                </div>
                                <button type="submit" class="btn btn-danger btn-modern">Deactivate Account</button>
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
            document.addEventListener('DOMContentLoaded', () => {
                const slotsContainer = document.getElementById('slots-container');
                const addSlotBtn = document.getElementById('add-slot');
                const maxSlots = 7;

                const createSlotRow = () => {
                    const currentSlotCount = slotsContainer.querySelectorAll('.slot-row').length;

                    if (currentSlotCount >= maxSlots) {
                        alert("You can only add up to 7 slots.");
                        return; // Prevent adding more slots
                    }

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
                <input type="text" name="slot_start_time[]" class="form-control slot-picker" placeholder="Start Time">
                <input type="text" name="slot_end_time[]" class="form-control slot-picker" placeholder="End Time">
                <button type="button" class="btn btn-outline-danger remove-slot"><i class="bx bx-x"></i></button>
            `;
                    slotsContainer.appendChild(div);
                    flatpickr(div.querySelectorAll('.slot-picker'), {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                    });
                };

                if (addSlotBtn) {
                    addSlotBtn.addEventListener('click', createSlotRow);
                }

                document.addEventListener('click', e => {
                    if (e.target.closest('.remove-slot')) {
                        e.target.closest('.slot-row').remove();
                    }
                });

                // Initialize all existing pickers
                flatpickr('.slot-picker', {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                });
            });
        </script>
        <script>
            document.getElementById('upload').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('uploadedAvatar').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endpush
