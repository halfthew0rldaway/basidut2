<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Validator;
class RegisterController extends Controller
{
     public function __invoke(Request $request)
{
    // 1. Validasi Input
    $validator = Validator::make($request->all(), [
        'username'     => 'required|unique:pengguna,username',
        'email'        => 'required|email|unique:pengguna,email',
        'kata_sandi'   => 'required|min:8',
        'nama_lengkap' => 'required',
    ]);

    // Jika validasi gagal
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // 2. Simpan Data ke Model Pengguna
    $user = Pengguna::create([
        'username'     => $request->username,
        'email'        => $request->email,
        'kata_sandi'   => bcrypt($request->kata_sandi),
        'nama_lengkap' => $request->nama_lengkap,
        'aktif'        => true, // Default aktif
    ]);

    // 3. Respon JSON
    if($user) {
        return response()->json([
            'success' => true,
            'message' => 'Registrasi Berhasil',
            'user'    => $user,  
        ], 201);
    }

    return response()->json([
        'success' => false,
        'message' => 'Registrasi Gagal',
    ], 409);
}
}
