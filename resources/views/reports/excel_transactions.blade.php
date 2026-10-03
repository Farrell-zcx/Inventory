<table>
    <thead>
    <tr>
        <th>Tanggal</th>
        <th>Barang</th>
        <th>Tipe</th>
        <th>Jumlah</th>
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
