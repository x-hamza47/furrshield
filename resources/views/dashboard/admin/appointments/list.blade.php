@extends('dashboard.main')

@push('styles')
<style>
    /* Container & Table */
    .table-modern {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
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
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    /* Rounded cells */
    .table-modern td, .table-modern th {
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

    .status-pending { background: linear-gradient(45deg, #f6b93b, #fa983a); }
    .status-approved { background: linear-gradient(45deg, #20bf6b, #01baef); }
    .status-completed { background: linear-gradient(45deg, #4a69bd, #6a89cc); }

    /* Action buttons */
    .btn-action {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
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

    <div class="table-responsive">
        <table class="table table-modern align-middle text-center">
            <thead >
                <tr >
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
                @foreach($appts as $appt)
                <tr>
                    <td>{{ $appts->firstItem() + $loop->index }}</td>
                    <td>{{ $appt->pet->name ?? 'N/A' }}</td>
                    <td>{{ $appt->owner->name ?? 'N/A' }}</td>
                    <td>{{ $appt->vet->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($appt->appt_date)->format('d M, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($appt->appt_time)->format('H:i') }}</td>
                    <td>
                        <span class="badge-status 
                            {{ $appt->status == 'pending' ? 'status-pending' : '' }}
                            {{ $appt->status == 'approved' ? 'status-approved' : '' }}
                            {{ $appt->status == 'completed' ? 'status-completed' : '' }}
                        ">{{ $appt->status }}</span>
                    </td>
                    <td class="d-flex justify-content-center gap-1">
                        <a href="{{ route('appts.show', $appt->id) }}" class="btn btn-info btn-action"><i class="bx bx-show"></i></a>
                        <a href="{{ route('appts.edit', $appt->id) }}" class="btn btn-warning btn-action"><i class="bx bx-edit-alt"></i></a>
                        <form action="{{ route('appts.destroy', $appt->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" onclick="return confirm('Are you sure?')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $appts->links() }}
    </div>
</div>
@endsection