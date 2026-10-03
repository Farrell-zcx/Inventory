<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\StockTransaction;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StockExport;
use App\Exports\TransactionExport;
use App\Exports\PurchaseOrderExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-reports');

        $tab = $request->get('tab', 'stock'); // stock, transactions, po

        // Data for Stock Report
        if ($tab === 'stock') {
            $categories = Category::all();
            $locations = Location::all();
            
            $query = Item::with(['category', 'location']);
            
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('location_id')) {
                $query->where('location_id', $request->location_id);
            }
            
            $items = $query->orderBy('name')->get();
            return view('reports.index', compact('tab', 'items', 'categories', 'locations'));
        }

        // Data for Transactions Report
        if ($tab === 'transactions') {
            $items = Item::orderBy('name')->get();
            $query = StockTransaction::with(['item', 'user', 'supplier']);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('item_id')) {
                $query->where('item_id', $request->item_id);
            }

            $transactions = $query->latest('transaction_date')->latest('id')->get();
            return view('reports.index', compact('tab', 'transactions', 'items'));
        }

        // Data for Purchase Orders Report
        if ($tab === 'po') {
            $query = PurchaseOrder::with(['requestedBy', 'approvedBy', 'items.item']);

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $purchaseOrders = $query->latest()->get();
            return view('reports.index', compact('tab', 'purchaseOrders'));
        }

        return redirect()->route('reports.index', ['tab' => 'stock']);
    }

    public function exportStock(Request $request)
    {
        Gate::authorize('view-reports');
        ini_set('memory_limit', '256M');
        set_time_limit(120);
        
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }

        $query = Item::with(['category', 'location']);
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);
        if ($request->filled('location_id')) $query->where('location_id', $request->location_id);
        $items = $query->orderBy('name')->get();

        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf_stock', compact('items'));
            return $pdf->download('laporan_stok_' . date('Ymd_His') . '.pdf');
        }

        return Excel::download(new StockExport($items), 'laporan_stok_' . date('Ymd_His') . '.xlsx');
    }

    public function exportTransactions(Request $request)
    {
        Gate::authorize('view-reports');
        ini_set('memory_limit', '256M');
        set_time_limit(120);
        
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }

        $query = StockTransaction::with(['item', 'user', 'supplier']);
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('item_id')) $query->where('item_id', $request->item_id);
        $transactions = $query->latest('transaction_date')->latest('id')->get();

        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf_transactions', compact('transactions'));
            return $pdf->download('laporan_transaksi_' . date('Ymd_His') . '.pdf');
        }

        return Excel::download(new TransactionExport($transactions), 'laporan_transaksi_' . date('Ymd_His') . '.xlsx');
    }

    public function exportPurchaseOrders(Request $request)
    {
        Gate::authorize('view-reports');
        ini_set('memory_limit', '256M');
        set_time_limit(120);
        
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
        }

        $query = PurchaseOrder::with(['requestedBy', 'approvedBy', 'items.item']);
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        $purchaseOrders = $query->latest()->get();

        $format = strtolower($request->get('format', 'excel'));

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf_po', compact('purchaseOrders'));
            return $pdf->download('laporan_po_' . date('Ymd_His') . '.pdf');
        }

        return Excel::download(new PurchaseOrderExport($purchaseOrders), 'laporan_po_' . date('Ymd_His') . '.xlsx');
    }
}
