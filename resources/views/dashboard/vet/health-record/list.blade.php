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
            background: linear-gradient(90deg, #11998e, #38ef7d);
            color: #fff;
        }

        .table-modern th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-modern tbody tr:hover {
            background: #f9fafb;
            transform: translateY(-2px);
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
    </style>
@endpush

@section('content')
    <div class="container">
        <h2 class="mb-4 text-success fw-bold"><i class="bx bx-heart"></i> Health Records</h2>

        {{-- Filters --}}
        <div class="filter-card mb-3">
            <form action="{{ route('health-records.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search Pet, Owner, or Vet"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('health-records.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-modern align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Pet</th>
                        <th class="text-white">Specie</th>
                        <th class="text-white">Owner</th>
                        <th class="text-white">Vet</th>
                        <th class="text-white">Diagnosis</th>
                        <th class="text-white">Treatment</th>
                        <th class="text-white">Visit Date</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $record->pet->name ?? 'N/A' }}</td>
                            <td>{{ $record->pet->species ?? 'N/A' }}</td>
                            <td>{{ $record->pet->owner->name ?? 'N/A' }}</td>
                            <td>{{ $record->vet->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($record->diagnosis, 20) }}</td>
                            <td>{{ Str::limit($record->treatment, 20) }}</td>
                            <td>{{ $record->visit_date ? \Carbon\Carbon::parse($record->visit_date)->format('d M, Y') : '-' }}
                            </td>
                            <td class="d-flex justify-content-center gap-1">
                                <!-- View -->
                                <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal"
                                    data-bs-target="#recordModal{{ $record->id }}">
                                    <i class="bx bx-show"></i>
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('health-records.pet.destroy', $record->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action"
                                        onclick="return confirm('Are you sure you want to delete this pet medical records ??')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal -->
                        <div class="modal fade" id="recordModal{{ $record->id }}" tabindex="-1"
                            aria-labelledby="recordModalLabel{{ $record->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content rounded-4 shadow">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title text-white" id="recordModalLabel{{ $record->id }}">
                                            <i class="bx bx-heart me-1"></i> Health Record History
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start" style="max-height: 400px; overflow-y: auto;">
                                        <h6 class="fw-bold mb-3">
                                            Pet: {{ $record->pet->name ?? 'N/A' }} ({{ $record->pet->species ?? 'N/A' }}) |
                                            Owner: {{ $record->pet->owner->name ?? 'N/A' }}
                                        </h6>

                                        @foreach ($record->pet->healthRecords as $h)
                                            <div class="mb-3 pb-3 border-bottom">
                                                <p><strong>Symptoms:</strong> {{ $h->symptoms ?? 'N/A' }}</p>
                                                <p><strong>Diagnosis:</strong> {{ $h->diagnosis ?? 'N/A' }}</p>
                                                <p><strong>Treatment:</strong> {{ $h->treatment ?? 'N/A' }}</p>
                                                <p><strong>Notes:</strong> {{ $h->notes ?? 'N/A' }}</p>

                                                @if (!empty($h->lab_reports))
                                                    <p><strong>Lab Reports:</strong></p>
                                                    <ul>
                                                        @foreach ($h->lab_reports as $test => $result)
                                                            <li><strong>{{ ucfirst($test) }}:</strong> {{ $result }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif

                                                <p><strong>Vet:</strong> {{ $h->vet->name ?? 'N/A' }}</p>
                                                <p><strong>Visit Date:</strong>
                                                    {{ $h->visit_date ? \Carbon\Carbon::parse($h->visit_date)->format('d M, Y') : '-' }}
                                                </p>
                                                <a href="{{ route('health-records.edit', $h->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bx bx-edit-alt"></i>
                                                </a>
                                                <form action="{{ route('health-records.destroy', $h->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-action"
                                                        onclick="return confirm('Are you sure you want to delete this pet medical records ??')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="text-muted">No health records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $records->links() }}
        </div>
    </div>
@endsection