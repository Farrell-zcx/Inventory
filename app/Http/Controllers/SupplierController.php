<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-master-data');
        $query = Supplier::latest();
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact', 'like', '%' . $request->search . '%');
        }
        $suppliers = $query->paginate(10)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        Gate::authorize('manage-suppliers');
        return view('suppliers.create');
    }

    public function show(Supplier $supplier)
    {
        Gate::authorize('manage-master-data');
        
        $transactions = \App\Models\StockTransaction::with(['item', 'user'])
            ->where('supplier_id', $supplier->id)
            ->latest('transaction_date')
            ->paginate(10);
            
        return view('suppliers.show', compact('supplier', 'transactions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-suppliers');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        Gate::authorize('manage-suppliers');
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        Gate::authorize('manage-suppliers');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'address' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        Gate::authorize('manage-suppliers');
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
