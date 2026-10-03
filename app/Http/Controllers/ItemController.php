<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-master-data');
        $query = Item::with(['category', 'location']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('condition') && $request->condition != '') {
            if ($request->condition == 'low_stock') {
                $query->where('current_stock', '<', 10);
            } else {
                $query->where('condition', $request->condition);
            }
        }

        $sort = $request->get('sort', 'latest');
        if ($sort == 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort == 'name_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sort == 'stock_desc') {
            $query->orderBy('current_stock', 'desc');
        } elseif ($sort == 'stock_asc') {
            $query->orderBy('current_stock', 'asc');
        } else {
            $query->latest();
        }

        $items = $query->paginate(10)->withQueryString();
        
        return view('items.index', compact('items'));
    }

    public function create()
    {
        Gate::authorize('manage-master-data');
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('items.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-master-data');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'location_id' => 'required|exists:locations,id',
            'unit' => 'required|string|max:50',
        ]);

        $item = Item::create($validated);

        return redirect()->route('items.show', $item->id)->with('warning', 'Barang berhasil ditambahkan. Stok masih 0, lakukan Transaksi Masuk untuk mengisi stok pertama kali.');
    }

    public function show(Item $item)
    {
        Gate::authorize('manage-master-data');
        $item->load(['category', 'location', 'stockTransactions.user', 'stockTransactions.supplier']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        Gate::authorize('manage-master-data');
        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(Request $request, Item $item)
    {
        Gate::authorize('manage-master-data');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'location_id' => 'required|exists:locations,id',
            'unit' => 'required|string|max:50',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        Gate::authorize('delete-master-data');
        
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($item) {
                // Delete related Purchase Order Items
                \App\Models\PurchaseOrderItem::where('item_id', $item->id)->delete();
                
                // Delete related Stock Transactions
                \App\Models\StockTransaction::where('item_id', $item->id)->delete();
                
                // Delete the item
                $item->delete();
            });
            return redirect()->route('items.index')->with('success', 'Barang beserta seluruh riwayat transaksinya berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('items.index')->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }
}
