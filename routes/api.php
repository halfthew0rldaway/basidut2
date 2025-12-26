<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| All routes here are automatically prefixed with /api
|
*/

// ============================================================================
// PUBLIC ROUTES - No Authentication Required
// ============================================================================

/**
 * Authentication Endpoints
 * 
 * POST /api/register - Register new user
 * POST /api/login    - Login and get JWT token
 */
Route::post('/register', RegisterController::class)->name('api.register');
Route::post('/login', LoginController::class)->name('api.login');

/**
 * Public Product Endpoints
 * 
 * GET /api/produk        - Get all products
 * GET /api/produk/{id}   - Get single product details
 */
Route::get('/produk', [ProdukController::class, 'index'])->name('api.produk.index');
Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('api.produk.show');

// ============================================================================
// PROTECTED ROUTES - JWT Authentication Required
// ============================================================================

Route::middleware('auth:api')->group(function () {
    
    // ------------------------------------------------------------------------
    // User Profile & Auth Management
    // ------------------------------------------------------------------------
    
    /**
     * GET /api/me      - Get authenticated user profile
     * POST /api/logout - Logout and invalidate token
     */
    Route::get('/me', function () {
        return response()->json([
            'success' => true,
            'user' => auth()->user()
        ]);
    })->name('api.me');
    
    Route::post('/logout', function () {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    })->name('api.logout');
    
    // ------------------------------------------------------------------------
    // Product Management (Admin/Protected)
    // ------------------------------------------------------------------------
    
    /**
     * POST   /api/produk        - Create new product
     * PUT    /api/produk/{id}   - Update product
     * DELETE /api/produk/{id}   - Delete product
     */
    Route::post('/produk', [ProdukController::class, 'store'])->name('api.produk.store');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('api.produk.update');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('api.produk.destroy');
    
    // ------------------------------------------------------------------------
    // Order Management
    // ------------------------------------------------------------------------
    
    /**
     * GET  /api/pesanan        - Get user's order history
     * GET  /api/pesanan/{id}   - Get single order details
     * POST /api/pesanan        - Create new order (uses stored procedure)
     */
    Route::prefix('pesanan')->name('api.pesanan.')->group(function () {
        Route::get('/', function () {
            $orders = \Illuminate\Support\Facades\DB::table('pesanan as p')
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
                    'pg.status_pengiriman',
                    'pr.nama as nama_produk',
                    'ip.jumlah',
                    'ip.harga_satuan'
                )
                ->where('p.pelanggan_id', auth()->id())
                ->orderBy('p.id', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        })->name('index');
        
        Route::get('/{id}', function ($id) {
            $pesanan = \App\Models\Pesanan::with(['itemPesanan.produk', 'pengiriman'])
                ->where('id', $id)
                ->where('pelanggan_id', auth()->id())
                ->first();
                
            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $pesanan
            ]);
        })->name('show');
        
        Route::post('/', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'product_id' => 'required|exists:produk,id',
                'qty' => 'required|integer|min:1',
                'courier' => 'required|string|max:50',
                'address' => 'required|string|max:500',
            ]);
            
            try {
                $userId = auth()->id();
                $productId = $request->product_id;
                $qty = $request->qty;
                $courier = $request->courier;
                $address = $request->address;
                
                // Call the stored procedure
                \Illuminate\Support\Facades\DB::statement(
                    'CALL sp_buat_pesanan_enterprise(?, ?, ?, ?, ?, @out_id, @out_status)',
                    [$userId, $productId, $qty, $courier, $address]
                );
                
                // Get the output variables
                $result = \Illuminate\Support\Facades\DB::select('SELECT @out_id as order_id, @out_status as status');
                
                if (!empty($result)) {
                    $output = $result[0];
                    
                    // Check for success
                    if (stripos($output->status, 'SUKSES') !== false || stripos($output->status, 'SUCCESS') !== false) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Pesanan berhasil dibuat!',
                            'order_id' => $output->order_id,
                        ], 201);
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
        })->name('store');
    });
    
    // ------------------------------------------------------------------------
    // Shipping Monitoring
    // ------------------------------------------------------------------------
    
    /**
     * GET /api/monitoring-pengiriman - Get shipping monitoring data
     * Uses the database view v_monitoring_pengiriman
     */
    Route::get('/monitoring-pengiriman', function () {
        try {
            $monitoring = \Illuminate\Support\Facades\DB::table('v_monitoring_pengiriman')
                ->where('pelanggan_id', auth()->id())
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $monitoring
            ]);
        } catch (\Exception $e) {
            // Fallback if view doesn't exist
            $monitoring = \Illuminate\Support\Facades\DB::table('pesanan as p')
                ->join('pengiriman as pg', 'p.id', '=', 'pg.pesanan_id')
                ->join('item_pesanan as ip', 'p.id', '=', 'ip.pesanan_id')
                ->join('produk as pr', 'ip.produk_id', '=', 'pr.id')
                ->select(
                    'p.id as pesanan_id',
                    'p.nomor_pesanan',
                    'p.pelanggan_id',
                    'p.total',
                    'p.status as status_pesanan',
                    'pg.kurir',
                    'pg.nomor_resi',
                    'pg.status_pengiriman',
                    'pr.nama as nama_produk',
                    'ip.jumlah'
                )
                ->where('p.pelanggan_id', auth()->id())
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $monitoring
            ]);
        }
    })->name('api.monitoring-pengiriman');
    
    // ------------------------------------------------------------------------
    // Audit Logs (Admin Only - Optional)
    // ------------------------------------------------------------------------
    
    /**
     * GET /api/audit-logs - Get audit logs for stock changes
     * Note: You may want to add admin middleware for this endpoint
     */
    Route::get('/audit-logs', function () {
        $logs = \Illuminate\Support\Facades\DB::table('log_audit')
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    })->name('api.audit-logs');
    
});

// ============================================================================
// HEALTH CHECK
// ============================================================================

/**
 * GET /api/health - API health check endpoint
 */
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Basidut API',
        'version' => '1.0.0'
    ]);
})->name('api.health');
