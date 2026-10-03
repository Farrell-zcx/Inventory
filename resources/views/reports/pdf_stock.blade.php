<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f3f4f6; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Stok Barang</h2>
        <p>Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>
    </div>
    <table>
        <thead>
        <tr>
            <th>Kode Aset</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <th>Stok</th>
            <th>Satuan</th>
            <th>Kondisi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category ? $item->category->name : '-' }}</td>
                <td>{{ $item->location ? $item->location->name : '-' }}</td>
                <td>{{ $item->current_stock }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->condition ? ucfirst(str_replace('_', ' ', $item->condition)) : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
