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

        .status-available {
            background: linear-gradient(45deg, #20bf6b, #01baef);
        }

        .status-outofstock {
            background: linear-gradient(45deg, #f6b93b, #fa983a);
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold"><i class="bx bx-package"></i> Product Listings</h2>
                <a href="{{ route('products.create') }}" class="btn btn-success">
                    <i class="bx bx-plus me-1"></i> Add Product
                </a>
        </div>

        <div class="filter-card">
            <form action="{{ route('products.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search Product Name"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available
                        </option>
                        <option value="outofstock" {{ request('status') == 'outofstock' ? 'selected' : '' }}>Out of Stock
                        </option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>
                            Discontinued</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="price_order" class="form-select">
                        <option value="">Price Order</option>
                        <option value="asc" {{ request('price_order') == 'asc' ? 'selected' : '' }}>Low to High</option>
                        <option value="desc" {{ request('price_order') == 'desc' ? 'selected' : '' }}>High to Low</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-modern align-middle text-center">
                <thead>
                    <tr>
                        <th class="text-white">#</th>
                        <th class="text-white">Image</th>
                        <th class="text-white">Product Name</th>
                        <th class="text-white">Category</th>
                        <th class="text-white">Price</th>
                        <th class="text-white">Stock</th>
                        <th class="text-white">Status</th>
                        <th class="text-white">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        @php
                            $status = $product->stock_quantity > 0 ? 'available' : 'outofstock';
                        @endphp
                        <tr>
                            <td>{{ $products->firstItem() + $loop->index }}</td>
                            <td>
                                <img src="{{ $product->pro_img ? asset('storage/' . $product->pro_img) : asset('dashboard/assets/img/avatars/dummy-1.jpeg') }}"
                                    alt="{{ $product->name }}" class="product-thumb ">
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category ?? 'N/A' }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->stock_quantity ?? 0 }}</td>
                            <td>
                                <span class="badge-status status-{{ $status }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="d-flex flex-wrap justify-content-center gap-2">
                                <button type="button" class="btn btn-info btn-action btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#productModal{{ $product->id }}">
                                    <i class="bx bx-show"></i>
                                </button>
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="btn btn-warning btn-action btn-sm">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-action btn-sm"
                                        onclick="return confirm('Are you sure?')">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>

                            {{-- Modal --}}
                            <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1"
                                aria-labelledby="productModalLabel{{ $product->id }}" aria-hidden="true"
                                data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title text-white" id="productModalLabel{{ $product->id }}">
                                                <i class="bx bx-package me-1"></i> Product Details
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <p><strong>Image:</strong></p>
                                            <img src="{{ $product->pro_img ? asset('storage/' . $product->pro_img) : asset('dashboard/assets/img/avatars/dummy-1.jpeg') }}"
                                                alt="{{ $product->name }}" class="img-fluid mb-3 rounded"
                                                style="max-width: 300px; max-height: 200px; object-fit: contain;">

                                            <p><strong>Name:</strong> {{ $product->name }}</p>
                                            <p><strong>Category:</strong> {{ $product->category ?? 'N/A' }}</p>
                                            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                            <p><strong>Stock:</strong> {{ $product->stock_quantity ?? 0 }}</p>
                                            <p><strong>Status:</strong>
                                                <span class="badge-status status-{{ $status }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </p>
                                            <p><strong>Description:</strong> {{ $product->description ?? 'N/A' }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-primary">Edit Product</a>
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
            {{ $products->links() }}
        </div>
    </div>
@endsection
