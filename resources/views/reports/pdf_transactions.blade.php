<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Stok</title>
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
        <h2>Laporan Transaksi Stok</h2>
        <p>Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>
    </div>
    <table>
        <thead>
        <tr>
            <th>Tanggal</th>
            <th>Barang</th>
            <th>Tipe</th>
            <th>Jml</th>
            <th>Peminjam/Supplier</th>
            <th>Status</th>
            <th>PIC</th>
            <th>Catatan</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $t)
            <tr>
                <td>{{ $t->transaction_date ? $t->transaction_date->format('Y-m-d') : ($t->created_at ? $t->created_at->format('Y-m-d') : '-') }}</td>
                <td>{{ $t->item ? $t->item->name : '-' }}</td>
                <td>{{ strtoupper($t->type ?? '-') }}</td>
                <td>{{ $t->quantity ?? 0 }}</td>
                <td>{{ $t->type == 'keluar' ? ($t->borrower_name ?? '-') : ($t->supplier ? $t->supplier->name : '-') }}</td>
                <td>{{ $t->status ? ucfirst(str_replace('_', ' ', $t->status)) : '-' }}</td>
                <td>{{ $t->user ? $t->user->name : '-' }}</td>
                <td>{{ $t->notes ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
