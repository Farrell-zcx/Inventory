<table>
    <thead>
    <tr>
        <th>Kode Aset</th>
        <th>Nama Barang</th>
        <th>Kategori</th>
        <th>Lokasi</th>
        <th>Stok Saat Ini</th>
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
