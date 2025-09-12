@extends('dashboard.main')

@push('styles')
    <style>
        .table-modern {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            font-size: 0.95rem;
        }

        .table-modern thead {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
        }

        .table-modern th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .table-modern td,
        .table-modern th {
            vertical-align: middle;
            border: none;
            padding: 12px 15px;
        }

        .badge-status {
            font-weight: 500;
            padding: 0.45em 0.75em;
            border-radius: 12px;
            color: #fff;
            text-transform: capitalize;
        }

        .status-available {
            background: linear-gradient(45deg, #20bf6b, #01baef);
        }

        .status-pending {
            background: linear-gradient(45deg, #f6b93b, #fa983a);
        }

        .status-adopted {
            background: linear-gradient(45deg, #4a69bd, #6a89cc);
        }

        .btn-action {
            font-size: 0.85rem;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .filter-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .filter-card input {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
@endpush
{{-- <pre>
    {{ $listings }}
</pre> --}}

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-heart"></i> Adoption Listings</h2>

        {{-- Filters --}}
        <div class="filter-card">
            <form action="{{ route('adoption.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search Pet or Breed"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available
                        </option>
                        <option value="adopted" {{ request('status') == 'adopted' ? 'selected' : '' }}>Adopted</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('adoption.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Pet Name</th>
                        <th class="text-white">Species</th>
                        <th class="text-white">Breed</th>
                        <th class="text-white">Age</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listings as $listing)
                        <tr>
                            <td>{{ $listings->firstItem() + $loop->index }}</td>
                            <td>{{ $listing->name }}</td>
                            <td>{{ $listing->species }}</td>
                            <td>{{ $listing->breed ?? 'N/A' }}</td>
                            <td>{{ $listing->age ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-status status-{{ $listing->status }}">
                                    {{ $listing->status }}
                                </span>
                            </td>
                            <td class="d-flex flex-wrap justify-content-center gap-2">
                                <!-- View Modal Button -->
                                <button type="button" class="btn btn-info btn-action btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#listingModal{{ $listing->id }}">
                                    <i class="bx bx-show"></i>
                                </button>

                                <!-- Edit Button -->
                                <a href="{{ route('adoption.edit', $listing->id) }}"
                                    class="btn btn-warning btn-action btn-sm">
                                    <i class="bx bx-edit-alt"></i>
                                </a>

                                <!-- Delete Button -->
                                <form action="{{ route('adoption.destroy', $listing->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action btn-sm"
                                        onclick="return confirm('Are you sure?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>

                            <!-- Modal -->
                            <div class="modal fade" id="listingModal{{ $listing->id }}" tabindex="-1"
                                aria-labelledby="listingModalLabel{{ $listing->id }}" aria-hidden="true"
                                data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white" id="listingModalLabel{{ $listing->id }}">
                                                <i class="bx bx-bone me-1"></i> Pet Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p><strong>Name:</strong> {{ $listing->name }}</p>
                                            <p><strong>Species:</strong> {{ $listing->species }}</p>
                                            <p><strong>Breed:</strong> {{ $listing->breed ?? 'N/A' }}</p>
                                            <p><strong>Age:</strong> {{ $listing->age ?? 'N/A' }}</p>
                                            <p><strong>Status:</strong>
                                                <span class="badge-status status-{{ $listing->status }}">
                                                    {{ $listing->status }}
                                                </span>
                                            </p>
                                            <p> <span
                                                    class="badge-status 
                                                        {{ $listing->status == 'available' ? 'status-approved' : '' }}
                                                        {{ $listing->status == 'pending' ? 'status-pending' : '' }}
                                                        {{ $listing->status == 'adopted' ? 'status-completed' : '' }}">
                                                    {{ $listing->status }}
                                                </span>
                                            </p>
                                            <p><strong>Shelter:</strong> {{ $listing->shelter->name ?? 'N/A' }}</p>

                                            @if ($listing->adopter)
                                                <p><strong>Adopted By:</strong> {{ $listing->adopter->name }}
                                                </p>
                                                <p><strong>Adoption Date:</strong>
                                                    {{ $listing->updated_at->format('d M Y') }}</p>
                                            @endif
                                            <p><strong>Description:</strong> {{ $listing->description ?? 'N/A' }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('adoption.edit', $listing->id) }}"
                                                class="btn btn-primary">Edit Listing</a>
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $listings->links() }}
        </div>
    </div>
@endsection
