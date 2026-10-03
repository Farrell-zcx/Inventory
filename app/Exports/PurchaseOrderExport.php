<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseOrderExport implements FromView, ShouldAutoSize
{
    protected $purchaseOrders;

    public function __construct($purchaseOrders)
    {
        $this->purchaseOrders = $purchaseOrders;
    }

    public function view(): View
    {
        return view('reports.excel_po', [
            'purchaseOrders' => $this->purchaseOrders
        ]);
    }
}
