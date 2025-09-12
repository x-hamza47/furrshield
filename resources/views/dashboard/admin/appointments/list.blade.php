@extends('dashboard.main')

@push('styles')
    <style>
        /* Container & Table */
        .table-modern {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            font-size: 0.95rem;
        }

        /* Header */
        .table-modern thead {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: #fff;
        }

        .table-modern th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Body */
        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* Rounded cells */
        .table-modern td,
        .table-modern th {
            vertical-align: middle;
            border: none;
            padding: 12px 15px;
        }

        /* Status badges */
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

        .status-completed {
            background: linear-gradient(45deg, #4a69bd, #6a89cc);
        }

        /* Action buttons */
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

        /* Responsive scroll */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-calendar-check"></i> Appointments</h2>

        {{-- Filters --}}
        <div class="filter-card">
            <form action="{{ route('appts.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search Pet, Owner, or Vet"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('appts.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Pet Name</th>
                        <th class="text-white">Owner Name</th>
                        <th class="text-white">Vet</th>
                        <th class="text-white">Date</th>
                        <th class="text-white">Time</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appts as $appt)
                        <tr>
                            <td>{{ $appts->firstItem() + $loop->index }}</td>
                            <td>{{ $appt->pet->name ?? 'N/A' }}</td>
                            <td>{{ $appt->owner->name ?? 'N/A' }}</td>
                            <td>{{ $appt->vet->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($appt->appt_date)->format('d M, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($appt->appt_time)->format('H:i') }}</td>
                            <td>
                                <span
                                    class="badge-status 
                            {{ $appt->status == 'pending' ? 'status-pending' : '' }}
                            {{ $appt->status == 'approved' ? 'status-approved' : '' }}
                            {{ $appt->status == 'completed' ? 'status-completed' : '' }}
                        ">{{ $appt->status }}</span>
                            </td>
                            <td class="d-flex justify-content-center gap-1">
                                <!-- View Modal Button -->
                                <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal"
                                    data-bs-target="#apptModal{{ $appt->id }}">
                                    <i class="bx bx-show"></i>
                                </button>

                                <a href="{{ route('appts.edit', $appt->id) }}" class="btn btn-warning btn-action"><i
                                        class="bx bx-edit-alt"></i></a>

                                <form action="{{ route('appts.destroy', $appt->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action"
                                        onclick="return confirm('Are you sure?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="apptModal{{ $appt->id }}" tabindex="-1"
                            aria-labelledby="apptModalLabel{{ $appt->id }}" aria-hidden="true"
                            data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 shadow">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title text-white" id="apptModalLabel{{ $appt->id }}"><i
                                                class="bx bx-calendar-check me-1"></i> Appointment Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <p><strong>Pet:</strong> {{ $appt->pet->name ?? 'N/A' }}</p>
                                        <p><strong>Owner:</strong> {{ $appt->owner->name ?? 'N/A' }}</p>
                                        <p><strong>Vet:</strong> {{ $appt->vet->name ?? 'N/A' }}</p>
                                        <p><strong>Date:</strong>
                                            {{ \Carbon\Carbon::parse($appt->appt_date)->format('d M, Y') }}</p>
                                        <p><strong>Time:</strong>
                                            {{ \Carbon\Carbon::parse($appt->appt_time)->format('H:i') }}</p>
                                        <p><strong>Status:</strong>
                                            <span
                                                class="badge-status 
                                        {{ $appt->status == 'pending' ? 'status-pending' : '' }}
                                        {{ $appt->status == 'approved' ? 'status-approved' : '' }}
                                        {{ $appt->status == 'completed' ? 'status-completed' : '' }}
                                    ">{{ $appt->status }}</span>
                                        </p>
                                        <p><strong>Owner Email:</strong> {{ $appt->owner->email ?? 'N/A' }}</p>
                                        <p><strong>Owner Phone:</strong> {{ $appt->owner->contact ?? 'N/A' }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('appts.edit', $appt->id) }}" class="btn btn-primary">Edit
                                            Appointment</a>
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $appts->links() }}
        </div>
    </div>
@endsection
