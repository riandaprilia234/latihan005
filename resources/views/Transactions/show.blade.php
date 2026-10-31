<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <!-- Card Header -->
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2">
            <h5 class="mb-0 fw-semibold">Detail Transaksi</h5>
            <div class="no-print">
                <button type="button" class="btn btn-light btn-sm text-dark me-1" onclick="window.print()">Cetak</button>
                <a href="{{ url('/transactions') }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
        </div>

        <div class="card-body">
            <!-- Informasi Header Transaksi -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="fw-bold" style="width: 120px;">No. Invoice</td>
                            <td style="width: 15px;">:</td>
                            <td>
                                <span class="badge bg-info text-dark px-2 py-1 fs-6">
                                    {{ $transaction->invoice_number }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Pelanggan</td>
                            <td>:</td>
                            <td>{{ $transaction->customer_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tanggal</td>
                            <td>:</td>
                            <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="fw-bold" style="width: 120px;">Keterangan</td>
                            <td style="width: 15px;">:</td>
                            <td>{{ $transaction->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Dibuat Pada</td>
                            <td>:</td>
                            <td>{{ $transaction->created_at ? \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Daftar Item -->
            <h6 class="fw-bold mb-3">Daftar Item</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Nama Item</th>
                            <th class="text-end" style="width: 200px;">Harga Satuan (Rp)</th>
                            <th class="text-center" style="width: 100px;">Qty</th>
                            <th class="text-end" style="width: 200px;">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $index => $detail)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $detail->item_name }}</td>
                            <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Tidak ada data item</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        @php
                            $subtotalAll = $details->sum('subtotal');
                            $diskonNominal = max(0, $subtotalAll - $transaction->total_amount);
                            $diskonPersen = $subtotalAll > 0 ? round(($diskonNominal / $subtotalAll) * 100) : 0;
                        @endphp
                        <tr>
                            <td colspan="4" class="text-end fw-bold border-0">Total:</td>
                            <td class="text-end fw-bold border-0">
                                Rp {{ number_format($subtotalAll, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold border-0">Diskon:</td>
                            <td class="text-end fw-bold text-danger border-0">
                                @if($diskonPersen > 0)
                                    {{ $diskonPersen }}% (Rp {{ number_format($diskonNominal, 0, ',', '.') }})
                                @else
                                    0% (Rp 0)
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold fs-5 border-0">Grand Total:</td>
                            <td class="text-end fw-bold text-primary fs-5 border-0">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>