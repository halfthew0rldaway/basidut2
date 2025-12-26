<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index()
    {
        $produk = Produk::where('stok', '>', 0)->get();

        return view('shop', compact('produk'));
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $produk = Produk::findOrFail($id);

        return view('produk.show', compact('produk'));
    }
}
