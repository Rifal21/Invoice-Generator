<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DeliveryOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryOrder::query();

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $deliveryOrders = $query->orderBy('date', 'desc')->orderBy('order_number', 'desc')->paginate(10)->withQueryString();

        return view('delivery-orders.index', compact('deliveryOrders'));
    }

    public function create()
    {
        return view('delivery-orders.create');
    }

    public function getNextNumber(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $datePrefix = Carbon::parse($date)->format('Ymd');

        $count = DeliveryOrder::whereDate('date', $date)->count();
        $nextNumber = 'SJ-' . $datePrefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return response()->json(['order_number' => $nextNumber]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|unique:delivery_orders',
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'location' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.quantity_string' => 'required|string',
            'items.*.item_name' => 'required|string',
            'items.*.description' => 'nullable|string',
        ]);

        $deliveryOrder = DeliveryOrder::create([
            'order_number' => $request->order_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'location' => $request->location,
        ]);

        foreach ($request->items as $item) {
            $deliveryOrder->items()->create([
                'quantity_string' => $item['quantity_string'],
                'item_name' => $item['item_name'],
                'description' => $item['description'],
            ]);
        }

        return redirect()->route('delivery-orders.index')->with('success', 'Surat Jalan berhasil dibuat.');
    }

    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items');
        return view('delivery-orders.show', compact('deliveryOrder'));
    }

    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items');
        return view('delivery-orders.edit', compact('deliveryOrder'));
    }

    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $request->validate([
            'order_number' => 'required|string|unique:delivery_orders,order_number,' . $deliveryOrder->id,
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'location' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.quantity_string' => 'required|string',
            'items.*.item_name' => 'required|string',
            'items.*.description' => 'nullable|string',
        ]);

        $deliveryOrder->update([
            'order_number' => $request->order_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'location' => $request->location,
        ]);

        $deliveryOrder->items()->delete();

        foreach ($request->items as $item) {
            $deliveryOrder->items()->create([
                'quantity_string' => $item['quantity_string'],
                'item_name' => $item['item_name'],
                'description' => $item['description'],
            ]);
        }

        return redirect()->route('delivery-orders.index')->with('success', 'Surat Jalan berhasil diperbarui.');
    }

    public function destroy(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->delete();
        return back()->with('success', 'Surat Jalan berhasil dihapus.');
    }

    public function exportPdf(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('items');
        $pdf = Pdf::loadView('delivery-orders.pdf', ['delivery' => $deliveryOrder]);
        return $pdf->stream('Surat Jalan ' . $deliveryOrder->order_number . '.pdf');
    }
}
