@extends('dashboard.main')

@push('styles')
    <style>
        /* Modern Table */
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
            vertical-align: middle;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .table-modern td {
            vertical-align: middle;
            border: none;
            padding: 12px 15px;
        }

        /* Role badges */
        .badge-role {
            padding: 0.4em 0.75em;
            border-radius: 12px;
            color: #fff;
            text-transform: capitalize;
            font-weight: 500;
        }

        .role-vet {
            background: linear-gradient(45deg, #20bf6b, #01baef);
        }

        .role-owner {
            background: linear-gradient(45deg, #f6b93b, #fa983a);
        }

        .role-shelter {
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

        /* Filters Card */
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

        .btn-action {
            width: auto;
            /* Do not stretch buttons */
            display: inline-flex;
            /* Keep buttons tight */
            justify-content: center;
            align-items: center;
            padding: 0.35rem 0.6rem;
            font-size: 0.85rem;
            border-radius: 8px;
        }

        td .d-flex.flex-wrap {
            justify-content: center;
            /* center buttons */
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
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-user"></i> Users</h2>

        <!-- Filter Card -->
        <div class="filter-card">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email or contact"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="vet" {{ request('role') == 'vet' ? 'selected' : '' }}>Vet</option>
                        <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Parent</option>
                        <option value="shelter" {{ request('role') == 'shelter' ? 'selected' : '' }}>Shelter</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern text-center align-middle">
                <thead>
                    <tr>
                        <th class="text-white">Name</th>
                        <th class="text-white">Email</th>
                        <th class="text-white">Role</th>
                        <th class="text-white">Contact</th>
                        <th class="text-white">Address</th>
                        <th class="text-white">Registered On</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ Str::limit($user->email, 20) }}</td>
                            <td>
                                <span
                                    class="badge-role 
                            {{ $user->role == 'vet' ? 'role-vet' : '' }}
                            {{ $user->role == 'owner' ? 'role-owner' : '' }}
                            {{ $user->role == 'shelter' ? 'role-shelter' : '' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->contact ?? 'N/A' }}</td>
                            <td>{{ Str::limit($user->address ?? 'N/A', 25) }}</td>
                            <td>{{ $user->created_at->format('F j, Y') }}</td>
                            <td>
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <!-- View Modal Trigger -->
                                    <button type="button" class="btn btn-info btn-action" data-bs-toggle="modal"
                                        data-bs-target="#userModal{{ $user->id }}">
                                        <i class="bx bx-show"></i>
                                    </button>

                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-action">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- User View Modal -->
                        <div class="modal fade" id="userModal{{ $user->id }}" tabindex="-1"
                            aria-labelledby="userModalLabel{{ $user->id }}" aria-hidden="true"
                            data-bs-backdrop="static" data-bs-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title text-white" id="userModalLabel{{ $user->id }}"><i
                                                class="bx bx-user-circle me-1"></i> {{ $user->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Name:</strong> {{ $user->name }}</p>
                                        <p><strong>Email:</strong> {{ $user->email }}</p>
                                        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                                        <p><strong>Contact:</strong> {{ $user->contact ?? 'N/A' }}</p>
                                        <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                                        <p><strong>Registered On:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Edit
                                            User</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted p-3">No Users Found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
