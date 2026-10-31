<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    // Menampilkan halaman utama
    public function index()
    {
        // buat ambil semua baris didatabase
        $item = DB::table('items')->get(); 

        if (view()->exists('items.index')) {
            return view('items.index', ['item' => $item]);
        }
        //ngirim data ke tampilan
        return view('index', ['item' => $item]);
    }

    // CREATE: Menyimpan data baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('items')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Item berhasil ditambahkan!'
        ]);
    }

    // UPDATE: Mengupdate data (ID diambil dari FormData)
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('items')
            ->where('id', $request->id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Item berhasil diupdate!'
        ]);
    }

    // DELETE: Menghapus data (ID diambil dari FormData)
    public function destroy(Request $request)
    {
        DB::table('items')
            ->where('id', $request->id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Item berhasil dihapus!'
        ]);
    }
}
