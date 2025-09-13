<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if (Auth::user()->role === 'shelter') {
            $query->where('shelter_id', Auth::user()->shelter->id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status == 'available') {
                $query->where('stock_quantity', '>', 0);
            } elseif ($status == 'outofstock') {
                $query->where('stock_quantity', '=', 0);
            } elseif ($status == 'discontinued') {
                $query->where('stock_quantity', '<', 0);
            }
        }
        if ($request->filled('price_order')) {
            $query->orderBy('price', $request->price_order);
        } else {
            $query->orderBy('id', 'desc');
        }

        $products = $query->paginate(10)->withQueryString();

        return view('dashboard.products.list', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if (!in_array($user->role, ['shelter', 'admin'])) {
            return redirect()->route('products.index')
                ->with('error', 'You are not authorized to create products.');
        }

        return view('dashboard.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only shelters and admins can create products
        if (!in_array(Auth::user()->role, ['shelter', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'pro_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity;
        $product->description = $request->description;

        // Assign shelter_id if shelter is logged in
        if (Auth::user()->role === 'shelter') {
            $product->shelter_id = Auth::user()->shelter->id;
        }

        // Handle image upload
        if ($request->hasFile('pro_img')) {
            $file = $request->file('pro_img');
            $extension = $file->extension();
            $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
            $path = $file->storeAs('products', $filename, 'public');
            $product->pro_img = $path;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id);

        if (Auth::user()->role === 'shelter' && $product->shelter_id !== Auth::user()->shelter->id) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->role === 'parent') {
            abort(403, 'Parents cannot edit products.');
        }

        return view('dashboard.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        if (Auth::user()->role === 'shelter' && $product->shelter_id !== Auth::user()->shelter->id) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user()->role === 'parent') {
            abort(403, 'Parents cannot update products.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'pro_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product->name = $request->name;
        $product->category = $request->category;
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity;
        $product->description = $request->description;

        if ($request->hasFile('pro_img')) {
            if ($product->pro_img) {
                $oldImagePath = public_path('storage/' . $product->pro_img);
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            $file = $request->file('pro_img');
            $extension = $file->extension();
            $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
            $path = $file->storeAs('products', $filename, 'public');

            $product->pro_img = $path;
        }

        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $user = Auth::user();


        if (!($user->role === 'admin' || ($user->role === 'shelter' && $product->shelter_id === $user->shelter->id))) {
            return redirect()->route('products.index')
                ->with('error', 'You are not authorized to delete this product.');
        }

        if ($product->pro_img && file_exists(storage_path('app/public/' . $product->pro_img))) {
            @unlink(storage_path('app/public/' . $product->pro_img));
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
