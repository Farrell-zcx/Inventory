<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-transactions');
        $query = StockTransaction::with(['item', 'user', 'supplier', 'reference']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(15);
        return view('stock_transactions.index', compact('transactions'));
    }

    public function create()
    {
        Gate::authorize('manage-transactions');
        $items = Item::all();
        $suppliers = Supplier::all();
        // Option to pass borrowed transactions if we are doing "pengembalian"
        $borrowedTransactions = StockTransaction::where('type', 'keluar')->where('status', 'dipinjam')->with('item')->get();

        return view('stock_transactions.create', compact('items', 'suppliers', 'borrowedTransactions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-transactions');
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'action_type' => 'required|in:masuk_beli,masuk_kembali,keluar',
            'quantity' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($request->action_type === 'masuk_beli') {
            $request->validate([
                'supplier_id' => 'nullable|exists:suppliers,id',
                'unit_price' => 'nullable|numeric|min:0',
                'total_price' => 'nullable|numeric|min:0',
            ]);
        } elseif ($request->action_type === 'masuk_kembali') {
            $request->validate([
                'reference_id' => 'required|exists:stock_transactions,id',
                'return_status' => 'required|in:dikembalikan,dikembalikan_rusak,hilang',
            ]);
        } elseif ($request->action_type === 'keluar') {
            $request->validate([
                'borrower_name' => 'required|string|max:255',
                'status' => 'required|in:dipinjam,habis',
            ]);
        }

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($request->item_id);
            $quantity = $request->quantity;

            if ($request->action_type === 'keluar') {
                if ($item->current_stock < $quantity) {
                    return back()->with('error', 'Stok tidak mencukupi untuk transaksi keluar. Stok saat ini: ' . $item->current_stock)->withInput();
                }
                $item->decrement('current_stock', $quantity);
            } else {
                if ($request->action_type === 'masuk_kembali') {
                    $refTransaction = StockTransaction::findOrFail($request->reference_id);
                    $refTransaction->update(['status' => $request->return_status]);

                    if ($request->return_status === 'dikembalikan_rusak') {
                        $item->update(['condition' => 'rusak_ringan']);
                    }
                }

                if (!($request->action_type === 'masuk_kembali' && $request->return_status === 'hilang')) {
                    $item->increment('current_stock', $quantity);
                }
            }

            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('documents', 'public');
            }

            StockTransaction::create([
                'item_id' => $request->item_id,
                'type' => $request->action_type === 'keluar' ? 'keluar' : 'masuk',
                'quantity' => $quantity,
                'supplier_id' => $request->action_type === 'masuk_beli' ? $request->supplier_id : null,
                'borrower_name' => $request->action_type === 'keluar' ? $request->borrower_name : null,
                'status' => $request->action_type === 'keluar' ? $request->status : ($request->action_type === 'masuk_kembali' ? $request->return_status : null),
                'reference_id' => $request->action_type === 'masuk_kembali' ? $request->reference_id : null,
                'unit_price' => $request->action_type === 'masuk_beli' ? $request->unit_price : null,
                'total_price' => $request->action_type === 'masuk_beli' ? $request->total_price : null,
                'document_path' => $documentPath,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'transaction_date' => $request->transaction_date,
            ]);

            DB::commit();
            return redirect()->route('stock_transactions.index')->with('success', 'Transaksi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockTransaction $stockTransaction)
    {
        Gate::authorize('manage-transactions');
        $stockTransaction->load(['item', 'user', 'supplier', 'reference']);
        return view('stock_transactions.show', compact('stockTransaction'));
    }
}
