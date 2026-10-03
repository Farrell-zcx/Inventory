<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-po');

        $query = PurchaseOrder::with(['requestedBy', 'approvedBy', 'items'])->latest();

        if (auth()->user()->role === 'staff') {
            $query->where('requested_by', auth()->id());
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('po_number', 'like', '%' . $request->search . '%');
        }

        $purchaseOrders = $query->paginate(10)->withQueryString();

        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        Gate::authorize('create-po');
        $items = Item::all();
        return view('purchase_orders.create', compact('items'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create-po');

        $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name_temp' => 'nullable|string|required_without:items.*.item_id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $po = PurchaseOrder::create([
                'status' => 'draft',
                'requested_by' => auth()->id(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name_temp' => $itemData['item_name_temp'] ?? null,
                    'quantity_requested' => $itemData['quantity_requested'],
                    'estimated_price' => $itemData['estimated_price'] ?? 0,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase_orders.index')->with('success', 'Draft Purchase Order berhasil dibuat.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('view-po');

        if (auth()->user()->role === 'staff' && $purchaseOrder->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $purchaseOrder->load(['requestedBy', 'approvedBy', 'items.item']);

        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create-po');

        if (auth()->user()->role === 'staff' && $purchaseOrder->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)->with('error', 'Hanya PO dengan status draft yang dapat diedit.');
        }

        $items = Item::all();
        $purchaseOrder->load('items');

        return view('purchase_orders.edit', compact('purchaseOrder', 'items'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create-po');

        if (auth()->user()->role === 'staff' && $purchaseOrder->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)->with('error', 'Hanya PO dengan status draft yang dapat diedit.');
        }

        $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name_temp' => 'nullable|string|required_without:items.*.item_id',
            'items.*.quantity_requested' => 'required|integer|min:1',
            'items.*.estimated_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder) {
            $purchaseOrder->update([
                'notes' => $request->notes,
            ]);

            $purchaseOrder->items()->delete();

            foreach ($request->items as $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name_temp' => $itemData['item_name_temp'] ?? null,
                    'quantity_requested' => $itemData['quantity_requested'],
                    'estimated_price' => $itemData['estimated_price'] ?? 0,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase_orders.show', $purchaseOrder)->with('success', 'Purchase Order berhasil diupdate.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create-po');

        if (auth()->user()->role === 'staff' && $purchaseOrder->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase_orders.show', $purchaseOrder)->with('error', 'Hanya PO dengan status draft yang dapat dihapus.');
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase_orders.index')->with('success', 'Purchase Order berhasil dihapus.');
    }

    public function submit(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('create-po');

        if (auth()->user()->role === 'staff' && $purchaseOrder->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Hanya PO dengan status draft yang dapat diajukan.');
        }

        $purchaseOrder->update(['status' => 'diajukan']);

        return back()->with('success', 'Purchase Order berhasil diajukan untuk approval.');
    }

    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('approve-po');

        if ($purchaseOrder->status !== 'diajukan') {
            return back()->with('error', 'PO ini tidak dalam status diajukan.');
        }

        $purchaseOrder->update([
            'status' => 'disetujui',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Purchase Order berhasil disetujui.');
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('approve-po');

        if ($purchaseOrder->status !== 'diajukan') {
            return back()->with('error', 'PO ini tidak dalam status diajukan.');
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $purchaseOrder->update([
            'status' => 'ditolak',
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Purchase Order berhasil ditolak.');
    }

    public function complete(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('manage-transactions');

        if ($purchaseOrder->status !== 'disetujui') {
            return back()->with('error', 'Hanya PO yang disetujui yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update(['status' => 'selesai']);

            foreach ($purchaseOrder->items as $poItem) {
                if ($poItem->item_id) {
                    \App\Models\StockTransaction::create([
                        'item_id' => $poItem->item_id,
                        'type' => 'masuk',
                        'quantity' => $poItem->quantity_requested,
                        'notes' => 'Penerimaan otomatis dari ' . $purchaseOrder->po_number,
                        'created_by' => auth()->id(),
                        'transaction_date' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', 'PO berhasil diselesaikan dan stok telah diperbarui.');
    }
}
