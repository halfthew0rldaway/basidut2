<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Produk;
class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar Data Produk',
            'data'    => $produks
        ], 200);
    }

    // POST: Menyimpan produk baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'        => 'required',
            'harga'       => 'required|numeric',
            'sku'         => 'required|unique:produk,sku',
            'stok'        => 'required|integer',
            'kategori_id' => 'required|exists:kategori,id', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $produk = Produk::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Produk Berhasil Ditambahkan',
            'data'    => $produk
        ], 201);
    }

    // GET: Menampilkan satu produk spesifik
    public function show($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $produk
        ], 200);
    }

    // PUT: Memperbarui data produk
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama'        => 'sometimes|string',
            'harga'       => 'sometimes|numeric',
            'sku'         => 'sometimes|unique:produk,sku,' . $id,
            'stok'        => 'sometimes|integer',
            'kategori_id' => 'sometimes|exists:kategori,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
$data = $request->only(['nama', 'harga', 'sku', 'stok', 'kategori_id']);
        $produk->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Produk Berhasil Diperbarui',
            'data'    => $produk->fresh()
        ], 200);
    }

    // DELETE: Menghapus produk
    public function destroy($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk Berhasil Dihapus'
        ], 200);
    }
}
