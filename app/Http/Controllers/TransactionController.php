<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    private function getTransactions()
    {
        return DB::table('transactions as m')
            ->leftJoin('transaction_details as d', 'm.id', '=', 'd.transaction_id')
            ->select(
                'm.id',
                'm.invoice_number',
                'm.customer_name',
                'm.transaction_date',
                'm.total_amount as grand_total',
                DB::raw('COALESCE(SUM(d.subtotal), m.total_amount) as total_before_discount')
            )
            ->groupBy('m.id', 'm.invoice_number', 'm.customer_name', 'm.transaction_date', 'm.total_amount')
            ->orderBy('m.id', 'asc')
            ->get();
    }

    public function index()
    {
        $transactions = $this->getTransactions();
        return view('Transactions.index', ['transactions' => $transactions]);
    }

    public function create()
    {
        $items = DB::table('items')->get();
        return view('Transactions.create', ['items' => $items]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:items,id',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        // 2. Jalankan Database Transaction
        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['qty'] * $item['price'];
            }

            $diskon = floatval($request->input('diskon', 0));
            $potongan = ($total * $diskon) / 100;
            $grandTotal = max(0, $total - $potongan);

            // Simpan Data Header Transaksi dan Ambil ID yang Baru Dibuat
            $invoiceNumber = 'TRX-' . date('YmdHis');
            $transactionId = DB::table("transactions")->insertGetId([
                'invoice_number' => $invoiceNumber,
                'customer_name' => $request->nama_pelanggan,
                'transaction_date' => $request->tanggal,
                'description' => $request->keterangan,
                'total_amount' => $grandTotal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Simpan Detail Transaksi dan Update Stok Barang
            foreach ($request->items as $item) {
                DB::table("transaction_details")->insert([
                    'transaction_id' => $transactionId,
                    'item_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Pengurangan stok barang di database
                DB::table("items")->where('id', $item['id'])->decrement('stock', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil disimpan!',
                'data' => [
                    'id' => $transactionId,
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $grandTotal
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        // 1. Ambil data header transaksi
        $transaction = DB::table('transactions')
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        // 2. Ambil data detail transaksi join dengan tabel items
        $details = DB::table('transaction_details')
            ->join('items', 'transaction_details.item_id', '=', 'items.id')
            ->select(
                'transaction_details.*',
                'items.name as item_name'
            )
            ->where('transaction_details.transaction_id', $id)
            ->get();

        // 3. Return view
        return view('Transactions.show', compact('transaction', 'details'));
    }

    public function rekap()
    {
        $transactions = $this->getTransactions();
        return view('Transactions.rekap', compact('transactions'));
    }

    public function rekapDetail()
    {
        $rekap = DB::table('transaction_details as d')
            ->join('items as i', 'i.id', '=', 'd.item_id')
            ->join('transactions as m', 'm.id', '=', 'd.transaction_id')
            ->select(
                'm.invoice_number',
                'm.customer_name',
                'm.transaction_date',
                'd.item_id',
                'i.name as item_name',
                'i.description',
                'd.price',
                'd.quantity',
                'd.subtotal'
            )
            ->get();

        return view('Transactions.rekap_detail', compact('rekap'));
    }
}
