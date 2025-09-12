@extends('dashboard.main')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-header bg-primary text-white rounded-top-4">
                        <h3 class="mb-0 text-white">
                            <i class="bx bx-paw me-2"></i>Edit Adoption: {{ $adoption->name }}
                        </h3>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ route('adoption.update', $adoption->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Animal Info -->
                            <h5 class="mb-3"><i class="bx bx-id-card me-1"></i> Animal Info</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror form-control-lg"
                                           value="{{ $adoption->name }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Species</label>
                                    <input type="text" name="species" class="form-control @error('species') is-invalid @enderror form-control-lg"
                                           value="{{ $adoption->species }}">
                                    @error('species')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Breed</label>
                                    <input type="text" name="breed" class="form-control @error('breed') is-invalid @enderror form-control-lg "
                                           value="{{ $adoption->breed }}">
                                    @error('breed')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Age</label>
                                    <input type="number" name="age" class="form-control @error('age') is-invalid @enderror form-control-lg"
                                           value="{{ $adoption->age }}">
                                    @error('age')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror form-select-lg">
                                        <option value="male" {{ $adoption->gender === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ $adoption->gender === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status + Shelter -->
                            <h5 class="mb-3"><i class="bx bx-home-heart me-1"></i> Adoption Info</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror form-select-lg">
                                        <option value="available" {{ $adoption->status === 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="adopted" {{ $adoption->status === 'adopted' ? 'selected' : '' }}>Adopted</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Shelter</label>
                                    <input type="text" class="form-control form-control-lg" 
                                           value="{{ $adoption->shelter->name ?? '' }}" disabled>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror form-control-lg" rows="4">{{ $adoption->description }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Image Upload -->
                            <div class="mb-4">
                                <label class="form-label">Animal Image</label>
                                <input type="file" name="image" class="form-control form-control-lg">
                                @if ($adoption->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $adoption->image) }}" 
                                             class="img-thumbnail rounded-3 shadow-sm" width="150">
                                    </div>
                                @endif
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Footer Buttons -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('adoption.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-lg">Update Adoption</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
