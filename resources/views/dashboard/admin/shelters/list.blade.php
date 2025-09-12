@extends('dashboard.main')
@push('styles')
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1.2rem;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.4em 0.6em;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bxs-home-heart"></i> Animal Shelters</h2>

        <div class="container mb-4">
            <form action="{{ route('shelter.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="shelter_name" class="form-control" placeholder="Shelter Name"
                        value="{{ request('shelter_name') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="contact_person" class="form-control" placeholder="Contact Person"
                        value="{{ request('contact_person') }}">
                </div>
                <div class="col-md-3">
                    <input type="text" name="address" class="form-control" placeholder="Address"
                        value="{{ request('address') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('shelter.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="row g-4">
            @foreach ($shelters as $shelter)
                <div class="col-md-4 col-sm-6">
                    <div class="card shadow-sm border-0 h-100 hover-shadow text-center align-items-center">
                        <div class="position-relative " style="width: 100px; height:100px;">
                            <img src="{{ $shelter->shelter->image ?? asset('dashboard/assets/img/illustrations/man-with-laptop-light.png') }}"
                                class="card-img-top rounded-circle mx-auto mt-3" alt="Shelter Image"
                                style="width:100%; height:100%; object-fit:cover; border:3px solid #fff; box-shadow:0 0 10px rgba(0,0,0,0.15);">
                        </div>

                        <div class="card-body d-flex flex-column align-items-center">
                            <h5 class="card-title fw-bold mt-2">{{ $shelter->shelter->shelter_name ?? 'N/A' }}</h5>
                            <p class="card-text mb-1"><i class="bx bx-map-pin me-1 text-muted"></i>
                                {{ $shelter->address ?? 'N/A' }}</p>
                            <p class="card-text mb-1"><i class="bx bx-user-circle me-1 text-muted"></i>
                                {{ $shelter->shelter->contact_person ?? 'N/A' }}</p>
                            <p class="card-text mb-3"><i class="bx bx-phone me-1 text-muted"></i>
                                {{ $shelter->contact ?? 'N/A' }}</p>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-info d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#shelterModal{{ $shelter->id }}">
                                    <i class="bx bx-show me-1"></i> View
                                </button>

                                <a href="{{ route('shelter.edit', $shelter->id) }}"
                                    class="btn btn-sm btn-warning d-flex align-items-center">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>

                                <form action="{{ route('users.destroy', $shelter->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Are you sure?')"
                                        class="btn btn-sm btn-danger d-flex align-items-center">
                                        <i class="bx bx-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shelter Modal -->
                <div class="modal fade" id="shelterModal{{ $shelter->id }}" tabindex="-1"
                    aria-labelledby="shelterModalLabel{{ $shelter->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title text-white" id="shelterModalLabel{{ $shelter->id }}"><i
                                        class="bx bx-building-house me-1"></i>
                                    {{ $shelter->shelter->shelter_name ?? 'N/A' }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Address:</strong> {{ $shelter->address ?? 'N/A' }}</p>
                                <p><strong>Contact Person:</strong> {{ $shelter->shelter->contact_person ?? 'N/A' }}</p>
                                <p><strong>Email:</strong> {{ $shelter->email ?? 'N/A' }}</p>
                                <p><strong>Phone:</strong> {{ $shelter->contact ?? 'N/A' }}</p>
                                <p><strong>Description:</strong> {{ $shelter->shelter->description ?? 'N/A' }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <a href="{{ route('shelter.edit', $shelter->id) }}" class="btn btn-primary">Edit
                                    Shelter</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="container bg-dark mt-4 align-items-center py-4">
            <div class=" d-flex justify-content-center ">
                {{ $shelters->links() }}
            </div>
        </div>
    </div>
@endsection
