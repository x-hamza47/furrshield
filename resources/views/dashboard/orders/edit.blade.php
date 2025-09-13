@extends('dashboard.main')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg rounded-4 border-0">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h3 class="mb-0 text-white">
                        <i class="bx bx-receipt me-2"></i> Edit Order: #{{ $order->id }}
                    </h3>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Owner Info -->
                        <h5 class="mb-3"><i class="bx bx-user me-1"></i> Owner Info</h5>
                        <div class="mb-4 d-flex align-items-center gap-3">
                            <img src="{{ $order->owner->profile_picture ? asset('storage/' . $order->owner->profile_picture) : asset('dashboard/assets/img/avatars/dummy-1.jpeg') }}"
                                 class="rounded-circle shadow-sm" width="60" height="60" alt="{{ $order->owner->name }}">
                            <div>
                                <h5 class="mb-0">{{ $order->owner->name }}</h5>
                                <small><i class="bx bx-envelope"></i> {{ $order->owner->email }}</small><br>
                                <small><i class="bx bx-phone"></i> {{ $order->owner->contact ?? 'N/A' }}</small>
                            </div>
                        </div>

                        <!-- Order Info -->
                        <h5 class="mb-3"><i class="bx bx-info-circle me-1"></i> Order Info</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Order Date</label>
                                <input type="date" name="order_date" 
                                       class="form-control form-control-lg @error('order_date') is-invalid @enderror"
                                       value="{{ $order->order_date->format('Y-m-d') }}"
                                       @if(Auth::user()->role !== 'admin') readonly @endif>
                                @error('order_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" 
                                        class="form-select form-select-lg @error('status') is-invalid @enderror"
                                        @if(Auth::user()->role === 'parent') disabled @endif>
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Order Items -->
                        <h5 class="mb-3"><i class="bx bx-package me-1"></i> Order Items</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price Each</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>
                                            <input type="number" name="quantities[{{ $item->id }}]" 
                                                   value="{{ $item->quantity }}" min="1" class="form-control"
                                                   @if(Auth::user()->role !== 'admin') readonly @endif>
                                        </td>
                                        <td>${{ number_format($item->price_each, 2) }}</td>
                                        <td>${{ number_format($item->quantity * $item->price_each, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Footer Buttons -->
                        @if(Auth::user()->role !== 'parent')
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg">Update Order</button>
                        </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
