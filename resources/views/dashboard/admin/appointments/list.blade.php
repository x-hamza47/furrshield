@extends('dashboard.main')

@push('styles')
<style>
    .table-modern {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .table-modern thead {
        background: linear-gradient(90deg, #4e73df, #1cc88a);
        color: #fff;
    }

    .table-modern th, .table-modern td {
        vertical-align: middle;
    }

    .table-modern tbody tr:hover {
        background-color: #f1f3f6;
        cursor: pointer;
    }

    .badge-status {
        padding: 0.4em 0.7em;
        font-size: 0.85rem;
        border-radius: 10px;
    }

    .btn-action {
        padding: 0.35rem 0.6rem;
        font-size: 0.85rem;
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
<div class="container">
    <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-calendar-check"></i> Appointments</h2>

    <div class="table-responsive">
        <table class="table table-modern align-middle text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pet Name</th>
                    <th>Owner Name</th>
                    <th>Vet</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appointment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $appointment->pet->name ?? 'N/A' }}</td>
                    <td>{{ $appointment->owner->name ?? 'N/A' }}</td>
                    <td>{{ $appointment->vet->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M, Y') }}</td>
                    <td>{{ $appointment->time }}</td>
                    <td>
                        @php
                            $status = strtolower($appointment->status);
                        @endphp
                        <span class="badge badge-status 
                            {{ $status == 'pending' ? 'bg-warning text-dark' : '' }}
                            {{ $status == 'confirmed' ? 'bg-success' : '' }}
                            {{ $status == 'canceled' ? 'bg-danger' : '' }}
                        ">{{ ucfirst($appointment->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-info btn-action"><i class="bx bx-show"></i></a>
                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-sm btn-warning btn-action"><i class="bx bx-edit-alt"></i></a>
                        <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Are you sure?')">
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
        {{ $appointments->links() }}
    </div>
</div>
@endsection
