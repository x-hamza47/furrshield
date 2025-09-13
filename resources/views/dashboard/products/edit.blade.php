@extends('dashboard.main')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg rounded-4 border-0">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h3 class="mb-0 text-white">
                        <i class="bx bx-package me-2"></i>Edit Product: {{ $product->name }}
                    </h3>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Product Info -->
                        <h5 class="mb-3"><i class="bx bx-info-circle me-1"></i> Product Info</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" 
                                       class="form-control @error('name') is-invalid @enderror form-control-lg"
                                       value="{{ $product->name }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" 
                                       class="form-control @error('category') is-invalid @enderror form-control-lg"
                                       value="{{ $product->category }}">
                                @error('category')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" name="price" 
                                       class="form-control @error('price') is-invalid @enderror form-control-lg"
                                       value="{{ $product->price }}">
                                @error('price')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity</label>
                                <input type="number" name="stock_quantity" 
                                       class="form-control @error('stock_quantity') is-invalid @enderror form-control-lg"
                                       value="{{ $product->stock_quantity }}">
                                @error('stock_quantity')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="form-label">Description</label>
                            <textarea name="description" 
                                      class="form-control @error('description') is-invalid @enderror form-control-lg" 
                                      rows="4">{{ $product->description }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label class="form-label">Product Image</label>

                            <!-- Show current image if exists -->
                            <div class="mb-3">
                                <img id="imagePreview" 
                                     src="{{ $product->pro_img && file_exists(storage_path('app/public/' . $product->pro_img)) ? asset('storage/' . $product->pro_img) : '#' }}" 
                                     class="img-fluid rounded-3 shadow-sm" style="max-width: 200px; {{ $product->pro_img ? '' : 'display:none;' }}">
                            </div>

                            <input type="file" name="pro_img" class="form-control form-control-lg" id="pro_img">
                            @error('pro_img')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Footer Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-lg">Update Product</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@push('scripts')
    <script>
    const proImgInput = document.getElementById('pro_img');
    const imagePreview = document.getElementById('imagePreview');

    proImgInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.setAttribute('src', e.target.result);
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
