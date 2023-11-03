<?php

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

Route::prefix('point-of-sale')->group(function () {

    Route::group(['prefix' => 'adjust-stock-type'], function () {
        Route::get('/', 'AdjustStockTypeController@all')->name('point-of-sale.adjust-stock-type.all');
        Route::post('/', 'AdjustStockTypeController@create')->name('point-of-sale.adjust-stock-type.create');
        Route::put('/{id}', 'AdjustStockTypeController@update')->name('point-of-sale.adjust-stock-type.update');
        Route::delete("/bulk-delete", "AdjustStockTypeController@bulkDelete");
        Route::get('/logs/{id}', 'AdjustStockTypeController@logs')->name('point-of-sale.adjust-stock-type.logs');
        Route::get('/{id}', 'AdjustStockTypeController@find')->name('point-of-sale.adjust-stock-type.find');
        Route::delete('/{id}', 'AdjustStockTypeController@delete')->name('point-of-sale.adjust-stock-type.delete');
    });

    Route::group(['prefix' => 'product'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\ProductController::class)->group(function () {
            Route::get('/user-login', 'getUserLogin');
            Route::get('inventories', 'inventories');
            Route::get('/', 'all')->name('product.index');
            Route::get('/{id}', 'find')->name('product.find');
            Route::post('/', 'create')->name('product.create');
            Route::post('/{id}', 'update')->name('product.update');
            Route::delete('/{id}', 'delete')->name('product.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('product.logs');
        });
    });

    Route::group(['prefix' => 'product-variants'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\ProductVariantController::class)->group(function () {
            Route::get('/', 'all')->name('product-variants.index');
            Route::get('/{id}', 'find')->name('product-variants.find');
            Route::post('/', 'create')->name('product-variants.create');
            Route::put('/{id}', 'update')->name('product-variants.update');
            Route::delete('/{id}', 'delete')->name('product-variants.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('product-variants.logs');
        });
    });

    Route::group(['prefix' => 'sales'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\SalesController::class)->group(function () {
            Route::get('products', 'salesProduct');
            Route::post('/', 'create')->name('sales.create');

        });
    });

    Route::group(['prefix' => 'oreders'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\OrederController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('get-return-orders', 'getReturnOrder');
            Route::post('/', 'createOrders');
            Route::post('return-order', 'returnOrder');
            Route::post('/hold', 'holdOrders');
            Route::post('/create-purchases-products', 'createPurchasesProducts');
            Route::post('/create-return-purchases-products', 'createReturnPurchasesProducts');
            Route::put('/update-hold/{id}', 'updateHoldOrders');
            Route::delete('/{id}', 'delete');
        });
    });

    Route::group(['prefix' => 'sales-list'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\SalesListController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });

    Route::group(['prefix' => 'cash-register'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\CashRegisterController::class)->group(function () {
            Route::get('/', 'all')->name('cash-registe.index');
            Route::get('/{id}', 'find')->name('cash-registe.find');
            Route::post('/', 'create')->name('cash-registe.create');
            Route::put('/{id}', 'update')->name('cash-registe.update');
            Route::delete('/{id}', 'delete')->name('cash-registe.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('cash-registe.logs');
        });
    });

    Route::group(['prefix' => 'sales-details'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\SalesDetailsController::class)->group(function () {
            Route::get('/getInvoiceId', 'getInvoiceId');
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });

    Route::group(['prefix' => 'sales-summary'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\SalesSummaryController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });

    Route::group(['prefix' => 'inventories'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\InventoryController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'payment-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\PaymentReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'payment-summary-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\PaymentSummaryReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'tax-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\TaxReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'ProfitLoss-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\ProfitLossReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'CustomerSummary-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\CustomerSummaryReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'SupplierSummary-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\SuppliersSummaryReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'SalesPurchases-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\SalesPurchasesReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });
    Route::group(['prefix' => 'Purchases-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\PurchaseReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });

    Route::group(['prefix' => 'PurchaseSummary-report'], function () {
        Route::controller(\Modules\PointOfSale\Http\Controllers\PurchaseSummaryReportController::class)->group(function () {
            Route::get('/', 'all');
            Route::get('/grandTotal', 'grandTotal');
        });
    });

});
