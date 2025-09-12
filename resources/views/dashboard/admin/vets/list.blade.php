@extends('dashboard.main')

@push('styles')
<style>
    /* Filter card - white theme */
    .filter-card {
        background: #fff;
        padding: 1rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 8px;
        border: 1px solid #d1d3e0;
        background: #fff;
        color: #495057;
    }
    .filter-card .btn {
        border-radius: 8px;
    }

    /* Vet cards */
    .vet-card {
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        background: #fff;
        transition: transform 0.2s;
    }
    .vet-card:hover {
        transform: scale(1.02);
    }
    .vet-card .avatar {
        font-weight: bold;
        font-size: 18px;
    }
    .vet-card .badge {
        border-radius: 10px;
        font-size: 0.8rem;
    }
    .vet-card .card-body {
        display: flex;
        flex-direction: column;
    }
    .vet-card .mt-auto {
        margin-top: auto;
    }

    /* Pagination */
    .pagination .page-item .page-link {
        border-radius: 8px;
        color: #2575fc;
    }
    .pagination .page-item.active .page-link {
        background: #2575fc;
        border-color: #2575fc;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-primary mb-3">
        <i class="bx bxs-bone me-2"></i> Veterinarians
    </h2>

    <form method="GET" action="{{ route('vets.index') }}" class="row g-3 align-items-center filter-card mt-3" >
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email"
                value="{{ request('search') }}">
        </div>

        <div class="col-md-4">
            <select name="specialization" class="form-select">
                <option value="">All Specializations</option>
                @foreach ($specializations as $spec)
                    <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>
                        {{ $spec }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="day" class="form-select">
                <option value="">Any Day</option>
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>
                        {{ $day }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
            <a href="{{ route('vets.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<div class="row g-4">
    @forelse($vets as $vet)
        <div class="col-md-4">
            <div class="vet-card shadow-sm border-0 h-100">
                <div class="card-body">
                    <!-- Header with avatar -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3"
                            style="width:50px; height:50px;">
                            {{ strtoupper(substr($vet->name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $vet->name }}</h5>
                            <small class="text-muted">{{ $vet->email }}</small>
                        </div>
                    </div>

                    <!-- Specialization & Experience -->
                    <div class="mb-2">
                        <span class="badge bg-success me-1">{{ $vet->vet->specialization ?? 'General' }}</span>
                        <span class="badge bg-info">{{ $vet->vet->experience ?? 0 }} yrs</span>
                    </div>

                    <!-- Available Slots -->
                    <div class="mb-3">
                        <strong>Available Slots:</strong>
                        <div class="mt-1 d-flex flex-wrap gap-1">
                            @if ($vet->vet && $vet->vet->available_slots)
                                @foreach (json_decode($vet->vet->available_slots) as $slot)
                                    <span class="badge bg-secondary d-flex align-items-center">
                                        <i class="bx bx-time me-1"></i> {{ $slot }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <a href="{{ route('vets.edit', $vet->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <span class="text-muted small">ID: {{ $vet->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">No vets found.</p>
    @endforelse
</div>

<!-- Pagination -->
<div class="mt-4 d-flex justify-content-center">
    {{ $vets->links() }}
</div>
@endsection
