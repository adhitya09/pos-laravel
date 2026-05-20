<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ROOT REDIRECT
    |--------------------------------------------------------------------------
    */

    Route::get('/', function () {

        $user = auth()->user();

        $first = $user->getFirstAccessibleRoute();

        if ($first) {
            return redirect()->route($first);
        }

        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    });


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.viewAny')
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [AuthController::class, 'showProfile'])
        ->name('profile.edit');

    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');


    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    /*
    |--------------------------------------------------------------------------
    | POS ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('pos', PosController::class)
        ->middleware('permission:pos.viewAny')
        ->only(['index', 'store']);

    Route::get('/pos/produk-by-barcode', [PosController::class, 'getProductByBarcode'])
        ->middleware('permission:pos.viewAny')
        ->name('pos.product-by-barcode');

    Route::get('/pos/scan-barcode', [PosController::class, 'scanBarcode'])
        ->middleware('permission:pos.viewAny')
        ->name('pos.scan-barcode');

    Route::get('/pos/resi/{transaction}', [PosController::class, 'cetakResi'])
        ->middleware('permission:pos.viewAny')
        ->name('pos.resi');


    /*
    |--------------------------------------------------------------------------
    | CATEGORY ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('kategori', KategoriController::class)
        ->middleware('permission:kategori.viewAny');


    /*
    |--------------------------------------------------------------------------
    | PRODUCT ROUTES
    |--------------------------------------------------------------------------
    */

    Route::get('/produk/cetak-barcode', [ProdukController::class, 'cetakBarcode'])
        ->middleware('permission:produk.viewAny')
        ->name('produk.cetak-barcode');

    Route::post('/produk/cetak-barcode-selected', [ProdukController::class, 'cetakBarcodeSelected'])
        ->middleware('permission:produk.viewAny')
        ->name('produk.cetak-barcode-selected');

    Route::post('/produk/bulk-delete', [ProdukController::class, 'bulkDelete'])
        ->middleware('permission:produk.viewAny')
        ->name('produk.bulk-delete');

    Route::post('/produk/reset-stok', [ProdukController::class, 'resetStok'])
        ->middleware('permission:produk.viewAny')
        ->name('produk.reset-stok');

    Route::resource('produk', ProdukController::class)
        ->middleware('permission:produk.viewAny');


    /*
    |--------------------------------------------------------------------------
    | INVENTORY ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('inventory', InventoryController::class)
        ->middleware('permission:inventory.viewAny');


    /*
    |--------------------------------------------------------------------------
    | PAYMENT METHOD ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('payment-method', PaymentMethodController::class)
        ->middleware('permission:payment-method.viewAny');

    Route::post(
        'payment-method/{payment_method}/restore',
        [PaymentMethodController::class, 'restore']
    )
        ->middleware('permission:payment-method.restore')
        ->name('payment-method.restore');


    /*
    |--------------------------------------------------------------------------
    | TRANSACTION ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('transaksi', TransaksiController::class)
        ->middleware('permission:transaksi.viewAny');

    Route::get('transaksi/{transaksi}/pdf', [TransaksiController::class, 'downloadPdf'])
        ->middleware('permission:transaksi.viewAny')
        ->name('transaksi.pdf');

    Route::patch('transaksi/{transaksi}/return', [TransaksiController::class, 'returnTransaction'])
        ->middleware('permission:transaksi.update')
        ->name('transaksi.return');


    /*
    |--------------------------------------------------------------------------
    | CASH FLOW ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('cash-flow', CashFlowController::class)
        ->middleware('permission:cash-flow.viewAny')
        ->only(['index', 'store', 'destroy']);


    /*
    |--------------------------------------------------------------------------
    | REPORT ROUTES
    |--------------------------------------------------------------------------
    */

    Route::get('/report', [ReportController::class, 'index'])
        ->middleware('permission:report.viewAny')
        ->name('report.index');

    Route::post('/report', [ReportController::class, 'store'])
        ->middleware('permission:report.viewAny')
        ->name('report.store');

    Route::patch('/report/{report}', [ReportController::class, 'update'])
        ->middleware('permission:report.viewAny')
        ->name('report.update');

    Route::get('/report/export/pdf', [ReportController::class, 'exportPdf'])
        ->middleware('permission:report.viewAny')
        ->name('report.export.pdf');

    Route::get('/report/export/excel', [ReportController::class, 'exportExcel'])
        ->middleware('permission:report.viewAny')
        ->name('report.export.excel');


    /*
    |--------------------------------------------------------------------------
    | USER ROUTES
    |--------------------------------------------------------------------------
    */

    Route::resource('user', UserController::class)
        ->middleware('permission:user.viewAny');

    Route::post('user/{user}/verify', [UserController::class, 'verify'])
        ->middleware('permission:user.update')
        ->name('user.verify');


    /*
    |--------------------------------------------------------------------------
    | ROLE ROUTES
    |--------------------------------------------------------------------------
    */

    Route::get('/role', [RoleController::class, 'index'])
        ->middleware('permission:role.viewAny')
        ->name('role.index');

    Route::get('/role/create', [RoleController::class, 'create'])
        ->middleware('permission:role.create')
        ->name('role.create');

    Route::post('/role', [RoleController::class, 'store'])
        ->middleware('permission:role.create')
        ->name('role.store');

    Route::get('/role/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:role.update')
        ->name('role.edit');

    Route::put('/role/{role}', [RoleController::class, 'update'])
        ->middleware('permission:role.update')
        ->name('role.update');

    Route::delete('/role/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:role.delete')
        ->name('role.destroy');


    /*
    |--------------------------------------------------------------------------
    | SETTINGS ROUTES
    |--------------------------------------------------------------------------
    */

    Route::get('/setting', [SettingController::class, 'index'])
        ->middleware('permission:setting.viewAny')
        ->name('setting.index');

    Route::put('/setting', [SettingController::class, 'update'])
        ->middleware('permission:setting.update')
        ->name('setting.update');

});
