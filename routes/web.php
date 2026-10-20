<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware('sso.auth')->group(function () {
    Route::get('/callback', function (\Illuminate\Http\Request $request) {
        $request->session()->save();
        return redirect('/dashboard');
    });

    Route::any('/logout', function () {
        $refreshToken = request()->session()->get('sso_refresh_token');
        
        if (!empty($refreshToken)) {
            try {
                $ssoInternalUrl = config('services.sso.internal_url') ?: config('services.sso.base_url') ?: env('SSO_BASE_URL', 'http://sso-engine.test');
                \Illuminate\Support\Facades\Http::timeout(5)->post(rtrim($ssoInternalUrl, '/') . '/logout', [
                    'refresh_token' => $refreshToken,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('[Inventory Logout] SSO logout failed: ' . $e->getMessage());
            }
        }

        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        $ssoBaseUrl = config('services.sso.base_url') ?: env('SSO_BASE_URL', 'http://sso-engine.test');
        $ssoLogoutUrl = rtrim($ssoBaseUrl, '/') . '/logout-web?redirect_to=' . urlencode(url('/'));
        return redirect($ssoLogoutUrl);
    })->name('logout');

    Route::get('/dashboard', function () {
        $totalItems = \App\Models\Item::count();
        $lowStockItemsCount = \App\Models\Item::where('current_stock', '<', 10)->count();
        $todayTransactions = \App\Models\StockTransaction::whereDate('transaction_date', \Carbon\Carbon::today())->count();
        $activeSuppliers = \App\Models\Supplier::count();
        
        // Chart Data: dynamically based on range (7H, 30H, YTD)
        $range = request('range', '7H');
        $endDate = \Carbon\Carbon::today();

        if ($range === '30H') {
            $startDate = \Carbon\Carbon::today()->subDays(29);
        } elseif ($range === 'YTD') {
            $startDate = \Carbon\Carbon::today()->startOfYear();
        } else {
            // default 7H
            $startDate = \Carbon\Carbon::today()->subDays(6);
        }

        $chartDataRaw = \App\Models\StockTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, type, SUM(quantity) as total')
            ->groupBy('date', 'type')
            ->get();
        
        $dates = [];
        $masukData = [];
        $keluarData = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $displayDate = $range === 'YTD' ? $currentDate->format('d M y') : $currentDate->format('d M');
            $dates[] = $displayDate;
            
            $masuk = $chartDataRaw->where('date', $dateStr)->where('type', 'masuk')->first();
            $keluar = $chartDataRaw->where('date', $dateStr)->where('type', 'keluar')->first();
            
            $masukData[] = $masuk ? (int) $masuk->total : 0;
            $keluarData[] = $keluar ? (int) $keluar->total : 0;
            
            $currentDate->addDay();
        }

        // Action Items (Items with low stock)
        $actionItems = \App\Models\Item::with('category')->where('current_stock', '<', 10)->get();

        return view('dashboard', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
            'totalItems' => $totalItems,
            'lowStockItems' => $lowStockItemsCount,
            'todayTransactions' => $todayTransactions,
            'activeSuppliers' => $activeSuppliers,
            'chartDates' => $dates,
            'chartMasuk' => $masukData,
            'chartKeluar' => $keluarData,
            'actionItems' => $actionItems,
        ]);
    })->name('dashboard');

    Route::resource('locations', \App\Http\Controllers\LocationController::class);
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::resource('items', \App\Http\Controllers\ItemController::class);
    Route::resource('stock_transactions', \App\Http\Controllers\StockTransactionController::class)->only(['index', 'create', 'store', 'show']);
    
    Route::post('purchase_orders/{purchase_order}/submit', [\App\Http\Controllers\PurchaseOrderController::class, 'submit'])->name('purchase_orders.submit');
    Route::post('purchase_orders/{purchase_order}/approve', [\App\Http\Controllers\PurchaseOrderController::class, 'approve'])->name('purchase_orders.approve');
    Route::post('purchase_orders/{purchase_order}/reject', [\App\Http\Controllers\PurchaseOrderController::class, 'reject'])->name('purchase_orders.reject');
    Route::post('purchase_orders/{purchase_order}/complete', [\App\Http\Controllers\PurchaseOrderController::class, 'complete'])->name('purchase_orders.complete');
    Route::resource('purchase_orders', \App\Http\Controllers\PurchaseOrderController::class);

    // Reports
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/stock', [\App\Http\Controllers\ReportController::class, 'exportStock'])->name('reports.export.stock');
    Route::get('reports/export/transactions', [\App\Http\Controllers\ReportController::class, 'exportTransactions'])->name('reports.export.transactions');
    Route::get('reports/export/po', [\App\Http\Controllers\ReportController::class, 'exportPurchaseOrders'])->name('reports.export.po');

    // Admin Dashboard (Kelola Pengguna)
    Route::middleware('admin')->group(function () {
        Route::get('/admin/kelola-pengguna', [\App\Http\Controllers\Admin\KelolaPenggunaController::class, 'index'])->name('admin.pengguna.index');
        Route::post('/admin/kelola-pengguna/provisioning', [\App\Http\Controllers\Admin\KelolaPenggunaController::class, 'storeProvisioning'])->name('admin.pengguna.storeProvisioning');
        Route::post('/admin/kelola-pengguna/{user}/role', [\App\Http\Controllers\Admin\KelolaPenggunaController::class, 'updateRole'])->name('admin.pengguna.updateRole');
    });
});

if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@inventory.local'],
            [
                'name' => 'Local Admin',
                'sso_user_id' => 'dev-admin-123',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'synced_at' => now(),
            ]
        );
        \Illuminate\Support\Facades\Auth::login($user);
        request()->session()->put('sso_token', 'dummy-token');
        return redirect('/dashboard');
    });

    Route::get('/dev-login/staff', function () {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'staff@inventory.local'],
            [
                'name' => 'Local Staff',
                'sso_user_id' => 'dev-staff-123',
                'role' => 'staff',
                'password' => bcrypt('password'),
                'synced_at' => now(),
            ]
        );
        \Illuminate\Support\Facades\Auth::login($user);
        request()->session()->put('sso_token', 'dummy-token-staff');
        return redirect('/dashboard');
    });

}
