<table>
    <thead>
    <tr>
        <th>Nomor PO</th>
        <th>Tanggal Dibuat</th>
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
