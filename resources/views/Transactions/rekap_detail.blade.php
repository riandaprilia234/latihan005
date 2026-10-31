<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rekap Detail Transaksi</title>
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Transactions - Rekap Detail</h2>
    
    <div class="mb-3">
        <a href="{{ url('/transactions/create') }}" class="btn btn-success btn-sm">Tambah</a>
        <a href="{{ url('/transactions/rekap') }}" class="btn btn-primary btn-sm">Rekap</a>
        <a href="{{ url('/transactions/rekap-detail') }}" class="btn btn-warning btn-sm">Rekap Detail</a>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>ID Item</th>
                <th>Nama Barang</th>
                <th>Keterangan</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekap as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->invoice_number }}</td>
                <td>{{ $row->customer_name ?? '-' }}</td>
                <td>{{ $row->transaction_date }}</td>
                <td>{{ $row->item_id }}</td>
                <td>{{ $row->item_name }}</td>
                <td>{{ $row->description ?? '-' }}</td>
                <td>Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                <td>{{ $row->quantity }}</td>
                <td>Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Belum ada data detail transaksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end align-items-center gap-4 mt-4">
        <h4 class="fw-bold mb-0">
            Total Qty: <span class="text-success">{{ number_format($rekap->sum('quantity'), 0, ',', '.') }}</span>
        </h4>
        <h4 class="fw-bold mb-0">
            Grand Total: <span class="text-primary">Rp {{ number_format($rekap->sum('subtotal'), 0, ',', '.') }}</span>
        </h4>
    </div>
</div>
</body>
</html>
