@extends('dashboard.main')


@section('content')
    <div>
        <div class="card mb-4 px-4 py-3">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Edit Info</h5>
                <a href="{{ route('users.index') }}" class="btn btn-primary text-white">Go back</a>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="basic-default-name">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="basic-default-name" placeholder="John Doe"
                                name="name" value="{{ $user->name }}" />
                        </div>
                    </div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="email">Email</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="email" placeholder="example@gmail.com"
                                name="email" value="{{ $user->email }}" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="contact">Contact</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="contact" placeholder="+389748937489"
                                name="contact" value="{{ $user->contact }}" />
                            @error('contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="address">Address</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="address" placeholder="abc street"
                                name="address" value="{{ $user->address }}" />
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if ($user->role == 'vet' && $user->vet)
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Available Slots</label>
                            <div class="col-sm-10">
                                <div id="slots-container">
                                    @php $slots = json_decode($user->vet->available_slots, true); @endphp
                                    @if ($slots)
                                        @foreach ($slots as $slot)
                                            <div class="mb-1 input-group">
                                                <input type="text" name="available_slots[]" class="form-control"
                                                    value="{{ $slot }}" placeholder="Mon 10:00-14:00">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-slot">Remove</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-slot">Add
                                    Slot</button>
                            </div>
                        </div>
                    @endif
                    @if ($user->role == 'shelter' && $user->shelter)
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Shelter Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="shelter_name"
                                    value="{{ $user->shelter->shelter_name }}" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Contact Person</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="contact_person"
                                    value="{{ $user->shelter->contact_person }}" />
                            </div>
                        </div>
                    @endif


                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('add-slot').addEventListener('click', function() {
            let container = document.getElementById('slots-container');
            let div = document.createElement('div');
            div.classList.add('mb-1', 'input-group');
            div.innerHTML = `
                <input type="text" name="available_slots[]" class="form-control" placeholder="Mon 10:00-14:00">
                <button type="button" class="btn btn-danger btn-sm remove-slot">Remove</button>
            `;
            container.appendChild(div);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-slot')) {
                e.target.parentElement.remove();
            }
        });
    </script>
@endpush
