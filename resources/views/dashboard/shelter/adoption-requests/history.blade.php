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

        .status-pending {
            background: linear-gradient(45deg, #f6b93b, #fa983a);
        }

        .status-approved {
            background: linear-gradient(45deg, #20bf6b, #01baef);
        }

        .status-rejected {
            background: linear-gradient(45deg, #eb3b5a, #fa3838);
        }

        .filter-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .filter-card input,
        .filter-card select {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-clipboard"></i> My Adoption Requests</h2>

        {{-- Filters --}}
        <div class="filter-card">
            <form action="{{ route('adoption-requests.history') }}" method="GET" class="row g-3 align-items-center">

                <!-- Status -->
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Single Search Box -->
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search Pet Name, Species or Adopter Name" value="{{ request('search') }}">
                </div>

                <!-- Sort by Date -->
                <div class="col-md-2">
                    <select name="sort_dir" class="form-select">
                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <!-- Filter and Reset Buttons -->
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('adoption-requests.history') }}" class="btn btn-outline-secondary w-100">Reset</a>
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
                        <th class="text-white">Adopter</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Message</th>
                        <th class="text-white">Requested At</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td>{{ $requests->firstItem() + $loop->index }}</td>
                            <td>{{ $request->adoption->name ?? 'N/A' }}</td>
                            <td>{{ $request->adoption->species ?? 'N/A' }}</td>
                            <td>{{ $request->adopter->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge-status status-{{ $request->status }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td>{{ Str::limit($request->message ?? '-', 20) }}</td>
                            <td>{{ $request->updated_at->format('d M Y') }}</td>
                            <td class="d-flex justify-content-center gap-2">
                                <!-- View Modal Button -->
                                <button type="button" class="btn btn-info btn-action btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#requestModal{{ $request->id }}">
                                    <i class="bx bx-show"></i>
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('adoption-requests.destroy', $request->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this request?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>

                            <!-- Modal -->
                            <div class="modal fade" id="requestModal{{ $request->id }}" tabindex="-1"
                                aria-labelledby="requestModalLabel{{ $request->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white" id="requestModalLabel{{ $request->id }}">
                                                <i class="bx bx-clipboard me-1"></i> Request Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p><strong>Pet Name:</strong> {{ $request->adoption->name ?? 'N/A' }}</p>
                                            <p><strong>Species:</strong> {{ $request->adoption->species ?? 'N/A' }}</p>
                                            <p><strong>Adopter:</strong> {{ $request->adopter->name ?? 'N/A' }}</p>
                                            <p><strong>Email:</strong> {{ $request->adopter->email ?? 'N/A' }}</p>
                                            <p><strong>Contact:</strong> {{ $request->adopter->contact ?? 'N/A' }}</p>
                                            <p><strong>Status:</strong>
                                                <span
                                                    class="badge-status status-{{ $request->status }}">{{ $request->status }}</span>
                                            </p>
                                            <p><strong>Message:</strong> {{ $request->message ?? '-' }}</p>
                                            <p><strong>Requested At:</strong>
                                                {{ $request->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No adoption requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
