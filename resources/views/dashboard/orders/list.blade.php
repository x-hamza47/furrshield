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

        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
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
            /* yellow/orange */
        }

        .status-completed {
            background: linear-gradient(45deg, #20bf6b, #01baef);
            /* green/blue */
        }

        .status-cancelled {
            background: linear-gradient(45deg, #eb3b5a, #fa5252);
            /* red */
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
        <h2 class="mb-4 text-primary fw-bold"><i class="bx bx-cart"></i> Orders</h2>

        {{-- Filters --}}
        <div class="filter-card mb-3">
            <form action="{{ route('orders.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by Owner Name"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled
                        </option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Order Date</th>
                        <th class="text-white">Owner</th>
                        <th class="text-white">Total Amount</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Items</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $orders->firstItem() + $loop->index }}</td>
                            <td>{{ $order->order_date->format('d M Y') }}</td>
                            <td>{{ $order->owner->name }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge-status status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>
                                <ul class="list-unstyled text-start">
                                    @foreach ($order->items as $item)
                                        <li>
                                            {{ $item->product->name ?? 'Deleted Product' }}
                                            x {{ $item->quantity }} (${{ number_format($item->price_each, 2) }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#orderModal{{ $order->id }}">
                                    <i class="bx bx-show"></i>
                                </button>
                                @if (!in_array(Auth::user()->role, ['parent']))
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>

                            {{-- Modal --}}
                            <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1"
                                aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content rounded-4 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">
                                                <i class="bx bx-cart me-1"></i> Order Details #{{ $order->id }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p><strong>Owner:</strong> {{ $order->owner->name }}</p>
                                            <p><strong>Order Date:</strong> {{ $order->order_date->format('d M Y') }}</p>
                                            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                                            <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}
                                            </p>
                                            <hr>
                                            <h6>Items:</h6>
                                            <ul>
                                                @foreach ($order->items as $item)
                                                    <li>
                                                        {{ $item->product->name ?? 'Deleted Product' }} - Qty:
                                                        {{ $item->quantity }},
                                                        ${{ number_format($item->price_each, 2) }}
                                                    </li>
                                                @endforeach
                                            </ul>
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
            {{ $orders->links() }}
        </div>
    </div>
@endsection
