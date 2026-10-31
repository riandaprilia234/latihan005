<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Transactions - Rekap</title>
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-3">Transactions</h2>
    
    <div class="mb-3">
        <a href="{{ url('/transactions/create') }}" class="btn btn-success btn-sm">Tambah</a>
        <a href="{{ url('/transactions/rekap') }}" class="btn btn-primary btn-sm">Rekap</a>
        <a href="{{ url('/transactions/rekap-detail') }}" class="btn btn-warning btn-sm">Rekap Detail</a>
    </div>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th style="width: 80px;">Aksi</th>
                <th>ID</th>
                <th>Kode</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th class="text-end">Total</th>
                <th class="text-center">Diskon</th>
                <th class="text-end">Grand Total</th>
            </tr>
        </thead>
        <tbody id="TransactionTableBody">
            @foreach($transactions as $transaction)
            @php
                $diskonNominal = max(0, $transaction->total_before_discount - $transaction->grand_total);
                $diskonPersen = $transaction->total_before_discount > 0 ? round(($diskonNominal / $transaction->total_before_discount) * 100) : 0;
            @endphp
            <tr>
                <td>
                    <a href="{{ url('/transactions/show/' . $transaction->id) }}" class="btn btn-warning btn-sm">View</a>
                </td>
                <td>{{ $transaction->id }}</td>
                <td>{{ $transaction->invoice_number }}</td>
                <td>{{ $transaction->customer_name ?? '-' }}</td>
                <td>{{ $transaction->transaction_date }}</td>
                <td class="text-end">Rp {{ number_format($transaction->total_before_discount, 0, ',', '.') }}</td>
                <td class="text-center">{{ $diskonPersen }}%</td>
                <td class="text-end fw-semibold">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-4">
        <h4 class="fw-bold">
            Grand Total: <span class="text-primary">Rp {{ number_format($transactions->sum('grand_total'), 0, ',', '.') }}</span>
        </h4>
    </div>
</div>
</body>
</html>
