<?php

use App\Http\Controllers\Api\v100\Admin\AuthController;
use App\Http\Controllers\Api\v100\Customer\CustomerAuthController;
use App\Http\Controllers\Api\v100\Customer\DraftInvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('api.checkApiKey')->prefix('v100')->group(function () {
    Route::get('test', function () {
        return 'test';
    });

    //admin
    Route::post('admin/login', [AuthController::class, 'login']);

//    Route::post('signup', [AuthController::class, 'signup']);
//    Route::post('password_reset', [ProfileController::class, 'passwordReset']);

    //Customer
    Route::post('customer/login', [CustomerAuthController::class, 'login']);
    Route::post('customer/signup', [CustomerAuthController::class, 'signup'])->name('api.customer.auth.store.customer');


    //common
    Route::get('countries',[\App\Http\Controllers\Api\v100\Common\CommonController::class,'getCountries'] );
    Route::get('states',[\App\Http\Controllers\Api\v100\Common\CommonController::class,'getStateByCountry'] );
    Route::get('cities',[\App\Http\Controllers\Api\v100\Common\CommonController::class,'getCitiesByState'] );

    Route::get('settings',[\App\Http\Controllers\Api\v100\Common\CommonController::class,'getSettings'] );


    Route::middleware(['jwt.verify'])->group(function () {

        Route::get('warehouses', [\App\Http\Controllers\Api\v100\Common\CommonController::class, 'warehouses']);
        Route::get('categories',[\App\Http\Controllers\Api\v100\Common\CommonController::class,'categories'] );

        Route::middleware(['api.checkIsAdmin'])->prefix('admin')->group(function () {
            //Admin
            Route::get('login_user/details', [AuthController::class, 'loginUserDetails']);
            Route::put('profile-update', [\App\Http\Controllers\Api\v100\Admin\ProfileController::class, 'update']);
            Route::get('roles', [\App\Http\Controllers\Api\v100\Admin\ProfileController::class, 'roles']);

            Route::get('home-page', [\App\Http\Controllers\Api\v100\Admin\HomePageController::class, 'index']);

            Route::get('top-product', [\App\Http\Controllers\Api\v100\Admin\HomePageController::class, 'getTopProduct']);
            Route::get('sale-chart-data', [\App\Http\Controllers\Api\v100\Admin\HomePageController::class, 'salesChartData']);
            Route::get('product-list', [\App\Http\Controllers\Api\v100\Admin\ProductController::class, 'index']);
            Route::get('stock-wise-products', [\App\Http\Controllers\Api\v100\Admin\ProductController::class, 'warehouseAndStockWiseProducts']);
            Route::get('purchase-list', [\App\Http\Controllers\Api\v100\Admin\PurchaseController::class, 'index']);
            //sale
            Route::get('sale-return-list', [\App\Http\Controllers\Api\v100\Admin\ProductSaleReturnController::class, 'index']);
            Route::get('sale-return-requests', [\App\Http\Controllers\Api\v100\Admin\ProductSaleReturnController::class, 'returnRequests']);
            //Invoice
            Route::get('invoice-list', [\App\Http\Controllers\Api\v100\Admin\InvoiceController::class, 'index']);
            Route::get('invoice-details/{id}', [\App\Http\Controllers\Api\v100\Admin\InvoiceController::class, 'show']);
            Route::get('invoice-download/{id}', [\App\Http\Controllers\Api\v100\Admin\InvoiceController::class, 'download']);

            //Report
            Route::get('expense-report', [\App\Http\Controllers\Api\v100\Admin\ReportController::class, 'expenseReport']);
            Route::get('sale-report', [\App\Http\Controllers\Api\v100\Admin\ReportController::class, 'sales']);
            Route::get('purchase-report', [\App\Http\Controllers\Api\v100\Admin\ReportController::class, 'purchases']);
            Route::get('payment-report', [\App\Http\Controllers\Api\v100\Admin\ReportController::class, 'payments']);
            Route::get('warehouse-stock-report', [\App\Http\Controllers\Api\v100\Admin\ReportController::class, 'warehouseStock']);
            Route::get('loss-profit-report', [\App\Http\Controllers\Api\v100\Admin\ReportController::class, 'lossProfit']);


            Route::post('logout', [AuthController::class, 'logout']);
        });
        //Customer
        Route::middleware(['api.checkIsCustomer'])->prefix('customer')->group(function () {
            Route::get('login_user/details', [CustomerAuthController::class, 'loginUserDetails']);
            Route::put('profile-update', [\App\Http\Controllers\Api\v100\Customer\ProfileController::class, 'update']);

            //home page
            Route::get('home-page', [\App\Http\Controllers\Api\v100\Customer\HomePageController::class, 'index']);
            Route::get('top-product', [\App\Http\Controllers\Api\v100\Customer\HomePageController::class, 'getTopProduct']);

            //invoice
            Route::post('invoice-create', [\App\Http\Controllers\Api\v100\Customer\InvoiceController::class, 'create']);
            Route::get('invoice-list', [\App\Http\Controllers\Api\v100\Customer\InvoiceController::class, 'index']);
            Route::get('invoice-details/{id}', [\App\Http\Controllers\Api\v100\Customer\InvoiceController::class, 'show']);
            Route::get('invoice-download/{id}', [\App\Http\Controllers\Api\v100\Customer\InvoiceController::class, 'download']);

            Route::get('draft-invoice-list', [DraftInvoiceController::class, 'draftInvoiceList']);
            Route::get('draft-invoice-details/{id}', [DraftInvoiceController::class, 'draftInvoiceDetails']);
            Route::delete('delete-draft-invoice/{id}', [DraftInvoiceController::class, 'destroy']);
            Route::post('draft-invoice-create', [DraftInvoiceController::class, 'create']);
            Route::put('draft-invoice-update/{id}', [DraftInvoiceController::class, 'update']);
            Route::post('invoices/store/from-draft/{id}', [DraftInvoiceController::class, 'storeDraftToInvoice']);

            //sale
            Route::get('returnable-invoice-list', [\App\Http\Controllers\Api\v100\Customer\InvoiceReturnController::class, 'index']);
            Route::get('invoice-return-requests', [\App\Http\Controllers\Api\v100\Customer\InvoiceReturnController::class, 'returnRequests']);

            //Report
            Route::get('purchase-report', [\App\Http\Controllers\Api\v100\Customer\ReportController::class, 'purchases']);
            Route::get('payment-report', [\App\Http\Controllers\Api\v100\Customer\ReportController::class, 'payments']);

            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

});
