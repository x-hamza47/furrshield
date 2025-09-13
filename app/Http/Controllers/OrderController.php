<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['owner', 'items.product']);

        if (Auth::user()->role === 'shelter') {
            $shelterId = Auth::user()->shelter->id ?? null;
            $query->whereHas('items.product', function ($q) use ($shelterId) {
                $q->where('shelter_id', $shelterId);
            });


            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('owner', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $orders = $query->orderBy('id', 'desc')->paginate(10);

            return view('dashboard.orders.list', compact('orders'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $order = Order::with('owner', 'items.product')->findOrFail($id);

        $user = Auth::user();

        if ($user->role === 'shelter') {
            $shelterProductIds = $user->shelter->products()->pluck('id')->toArray();
            $orderItemProductIds = $order->items->pluck('product_id')->toArray();

            if (!array_intersect($shelterProductIds, $orderItemProductIds)) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($user->role === 'parent') {
            abort(403, 'Parents cannot edit orders.');
        }

        return view('dashboard.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::with('items.product', 'owner')->findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'parent') {
            abort(403, 'Parents cannot update orders.');
        }

        if ($user->role === 'shelter') {
            $shelterProductIds = $user->shelter->products->pluck('id')->toArray();
            $orderProductIds = $order->items->pluck('product_id')->toArray();

            if (!array_intersect($shelterProductIds, $orderProductIds)) {
                abort(403, 'Unauthorized action. This order does not contain your products.');
            }

            $request->validate([
                'status' => 'required|in:pending,completed,cancelled',
            ]);

            $order->status = $request->status;
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Order status updated successfully.');
        }

        if ($user->role === 'admin') {
            $request->validate([
                'order_date' => 'required|date',
                'status' => 'required|in:pending,completed,cancelled',
                'quantities' => 'required|array',
                'quantities.*' => 'required|integer|min:1',
            ]);

            $totalAmount = 0;

            foreach ($order->items as $item) {
                if (isset($request->quantities[$item->id])) {
                    $item->quantity = $request->quantities[$item->id];
                    $item->save();
                    $totalAmount += $item->quantity * $item->price_each;
                }
            }

            $order->order_date = $request->order_date;
            $order->status = $request->status;
            $order->total_amount = $totalAmount;
            $order->save();

            return redirect()->route('orders.index')->with('success', 'Order updated successfully.');
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'parent') {
            abort(403, 'Parents cannot delete orders.');
        }

        if ($user->role === 'shelter') {
            $shelterProductIds = $user->shelter->products->pluck('id')->toArray();
            $orderProductIds = $order->items->pluck('product_id')->toArray();

            if (!array_intersect($shelterProductIds, $orderProductIds)) {
                abort(403, 'Unauthorized action. This order does not contain your products.');
            }

            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
        }

        if ($user->role === 'admin') {
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
        }

        abort(403, 'Unauthorized action.');
    }
}
