<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        // Query pesanan table with joins instead of view
        $orders = DB::table('pesanan as p')
            ->leftJoin('pengiriman as pg', 'p.id', '=', 'pg.pesanan_id')
            ->leftJoin('item_pesanan as ip', 'p.id', '=', 'ip.pesanan_id')
            ->leftJoin('produk as pr', 'ip.produk_id', '=', 'pr.id')
            ->select(
                'p.id',
                'p.nomor_pesanan',
                'p.total',
                'p.status',
                'p.tanggal_pesanan',
                'pg.kurir',
                'pg.nomor_resi',
                'pr.nama as nama_produk',
                'ip.jumlah'
            )
            ->where('p.pelanggan_id', Auth::id())
            ->orderBy('p.id', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $pesanan = Pesanan::with(['itemPesanan.produk', 'pengiriman'])
            ->where('id', $id)
            ->where('pelanggan_id', Auth::id())
            ->firstOrFail();

        return view('orders.show', compact('pesanan'));
    }

    /**
     * Store a newly created order using stored procedure.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:produk,id',
            'qty' => 'required|integer|min:1',
            'courier' => 'required|string|max:50',
            'address' => 'required|string|max:500',
        ]);

        try {
            $userId = Auth::id();
            $productId = $request->product_id;
            $qty = $request->qty;
            $courier = $request->courier;
            $address = $request->address;

            // Call the stored procedure
            DB::statement(
                'CALL sp_buat_pesanan_enterprise(?, ?, ?, ?, ?, @out_id, @out_status)',
                [$userId, $productId, $qty, $courier, $address]
            );

            // Get the output variables
            $result = DB::select('SELECT @out_id as order_id, @out_status as status');

            if (!empty($result)) {
                $output = $result[0];

                // Check for success (handle both SUKSES and SUCCESS)
                if (stripos($output->status, 'SUKSES') !== false || stripos($output->status, 'SUCCESS') !== false) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pesanan berhasil dibuat!',
                        'order_id' => $output->order_id,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $output->status,
                    ], 400);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat pesanan.',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
