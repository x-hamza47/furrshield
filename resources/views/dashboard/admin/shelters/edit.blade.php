@extends('dashboard.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h3 class="mb-0 text-white"><i class="bx bx-building-house me-2"></i>Edit Shelter:
                            {{ $shelter->shelter->shelter_name ?? '' }}</h3>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ route('users.update', $shelter->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- User Info -->
                            <h5 class="mb-3"><i class="bx bx-id-card me-1"></i> User Info</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        value="{{ $shelter->name }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg"
                                        value="{{ $shelter->email }}">
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Contact</label>
                                    <input type="text" name="contact" class="form-control form-control-lg"
                                        value="{{ $shelter->contact }}">
                                    @error('contact')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" class="form-control form-control-lg"
                                        value="{{ $shelter->address }}">
                                    @error('address')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Shelter Info -->
                            <h5 class="mb-3"><i class="bx bx-info-circle me-1"></i> Shelter Info</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Shelter Name</label>
                                    <input type="text" name="shelter_name" class="form-control form-control-lg"
                                        value="{{ $shelter->shelter->shelter_name ?? '' }}">
                                    @error('shelter_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control form-control-lg"
                                        value="{{ $shelter->shelter->contact_person ?? '' }}">
                                    @error('contact_person')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control form-control-lg" rows="4">{{ $shelter->shelter->description ?? '' }}</textarea>
                                    @error('description')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Footer Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('shelter.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg">Update Shelter</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
