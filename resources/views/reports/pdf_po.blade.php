<!DOCTYPE html>
<html>
<head>
    <title>Laporan Purchase Orders</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f3f4f6; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Purchase Orders</h2>
        <p>Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>
    </div>
    <table>
        <thead>
        <tr>
            <th>Nomor PO</th>
            <th>Tgl Dibuat</th>
            <th>Pemohon</th>
            <th>Status</th>
            <th>Total Item</th>
            <th>Barang Diminta</th>
            <th>Estimasi Harga</th>
        </tr>
        </thead>
        <tbody>
        @foreach($purchaseOrders as $po)
            <tr>
                <td>{{ $po->po_number }}</td>
                <td>{{ $po->created_at ? $po->created_at->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $po->requestedBy ? $po->requestedBy->name : '-' }}</td>
                <td>{{ strtoupper($po->status ?? '-') }}</td>
                <td>{{ $po->items->sum(fn($item) => $item->quantity_requested ?? 0) }}</td>
                <td>
                    @foreach($po->items as $i)
                        {{ $i->item ? $i->item->name : ($i->item_name_temp ?: '-') }} ({{ $i->quantity_requested ?? 0 }}); 
                    @endforeach
                </td>
                <td>
                    {{ number_format($po->items->sum(fn($item) => $item->estimated_price ?? 0), 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
