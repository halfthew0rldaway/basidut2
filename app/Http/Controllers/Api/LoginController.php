<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'kata_sandi' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 2. Mapping Credentials
// Kita ambil 'email' dan 'kata_sandi' dari request, 
// tapi kita ubah key 'kata_sandi' menjadi 'password' agar dimengerti oleh sistem Auth
        $credentials = [
            'email' => $request->email,
            'password' => $request->kata_sandi, // 'password' di sini adalah kunci internal Laravel
        ];

        // 3. Attempt Auth
        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Kata Sandi Anda salah'
            ], 401);
        }

        // 4. Return Success
        return response()->json([
            'success' => true,
            'user' => auth()->guard('api')->user(),
            'token' => $token
        ], 200);
    }
}
