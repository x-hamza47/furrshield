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
@section('content')
    <div class="container mt-4">
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-clipboard"></i> Adoption Requests</h2>

        {{-- Filters --}}
        <div class="filter-card">
            <form action="{{ route('adoption-requests.index') }}" method="GET" class="row g-3 align-items-center">

                <!-- Search -->
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search Pet or Adopter"
                        value="{{ request()->filled('search') ? request('search') : '' }}">
                </div>

                <!-- Sort By -->
                <div class="col-md-2">
                    <select name="sort_by" class="form-select">
                        <option value="">Sort By</option>
                        <option value="created_at"
                            {{ request()->filled('sort_by') && request('sort_by') == 'created_at' ? 'selected' : '' }}>Date
                        </option>
                        <option value="adoption_id"
                            {{ request()->filled('sort_by') && request('sort_by') == 'adoption_id' ? 'selected' : '' }}>Pet
                            ID</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div class="col-md-2">
                    <select name="sort_dir" class="form-select">
                        <option value="asc"
                            {{ request()->filled('sort_dir') && request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascending
                        </option>
                        <option value="desc"
                            {{ request()->filled('sort_dir') && request('sort_dir') == 'desc' ? 'selected' : '' }}>
                            Descending</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('adoption-requests.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>

            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Pet Name</th>
                        <th class="text-white">Adopter</th>
                        <th class="text-white">Message</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Requested At</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr>
                            <td>{{ $requests->firstItem() + $loop->index }}</td>
                            <td>{{ $request->adoption->name ?? 'N/A' }}</td>
                            <td>{{ $request->adopter->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($request->message ?? '-', 30) }}</td>
                            <td>
                                <span class="badge-status status-{{ $request->status }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d M Y') }}</td>
                            <td class="d-flex flex-wrap justify-content-center gap-2">
                                <!-- View Modal Button -->
                                <button type="button" class="btn btn-info btn-action btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#requestModal{{ $request->id }}">
                                    <i class="bx bx-show"></i>
                                </button>

                                <!-- Approve / Reject Buttons -->
                                @if ($request->status == 'pending')
                                    <!-- Approve Button -->
                                    <form action="{{ route('adoption-requests.index', $request->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-action btn-sm">
                                            <i class="bx bx-check"></i> <!-- check icon -->
                                        </button>
                                    </form>

                                    <!-- Reject Button -->
                                    <form action="{{ route('adoption-requests.index', $request->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-action btn-sm">
                                            <i class="bx bx-x"></i> <!-- cross icon -->
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- Modal -->
                            <div class="modal fade" id="requestModal{{ $request->id }}" tabindex="-1"
                                aria-labelledby="requestModalLabel{{ $request->id }}" aria-hidden="true"
                                data-bs-backdrop="static" data-bs-keyboard="false">
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
                                            <p><strong>Adopter:</strong> {{ $request->adopter->name ?? 'N/A' }}</p>
                                            <p><strong>Email:</strong> {{ $request->adopter->email ?? 'N/A' }}</p>
                                            <p><strong>Contact:</strong> {{ $request->adopter->contact ?? 'N/A' }}</p>
                                            <p><strong>Status:</strong>
                                                <span
                                                    class="badge-status status-{{ $request->status }}">{{ $request->status }}</span>
                                            </p>
                                            <p><strong>Message:</strong> {{ $request->message ?? '-' }}</p>
                                            <p><strong>Requested At:</strong> {{ $request->created_at->format('d M Y') }}
                                            </p>
                                        </div>
                                        <div class="modal-footer">
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
            {{ $requests->links() }}
        </div>
    </div>
@endsection
