@extends('dashboard.main')

@section('content')
    <div class="card">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-header d-flex">All Users List</h5>
            {{-- @can('parent-view') --}}
                {{-- <span class="px-4">
                    <a href=""class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="bx bx-plus fs-5 fw-bold"></i>
                        <p class="m-0" style="line-height: 1.9">Add User</p>
                    </a>
                </span> --}}
            {{-- @endcan --}}
        </div>
        <div class="px-4">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-center mb-4">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control" placeholder="Search child or parent"
                        value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="role" class="form-select">
                        <option value="">Filter Roles</option>
                        <option value="vet" {{ request('role') == 'vet' ? 'selected' : '' }}>Vet</option>
                        <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Parent</option>
                        <option value="shelter" {{ request('role') == 'shelter' ? 'selected' : '' }}>Shelter</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-bordered  text-center">
                <thead>
                    <tr class="align-middle table-dark">
                        <th  class="fw-bold">Name</th>
                        <th  class="fw-bold">Email</th>
                        <th  class="fw-bold">Role</th>
                        <th  class="fw-bold">Contact</th>
                        <th  class="fw-bold">Address</th>
                        <th  class="fw-bold">Registered On</th>
                        <th  class="fw-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($users as $user)
                        
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>{{ ucfirst($user->contact) }}</td>
                            <td>{{ ucfirst($user->address) }}</td>
                            <td>{{ $user->created_at->format('F j, Y') }}</td>
                            <td class="d-flex gap-2 align-items-center justify-content-center">
                                <a class="btn-primary btn-sm btn fs-4 text-white d-flex align-items-center justify-content-center"
                                    href="{{ route('users.edit', $user->id) }}" data-bs-toggle="tooltip"
                                    data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true"
                                    title="<i class='bx bx-edit' ></i> <span>Edit User</span>">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-sm btn-danger fs-4 text-white d-flex align-items-center justify-content-center"
                                        data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top"
                                        data-bs-html="true" title="<i class='bx bx-trash'></i> <span>Delete User</span>">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted p-3">No Data found.</td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
            <div class="mt-3 px-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
