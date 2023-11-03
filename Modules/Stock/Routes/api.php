<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Stock\Http\Controllers\AllTransactionController;
use Modules\Stock\Http\Controllers\ClosingBalanceController;
use Modules\Stock\Http\Controllers\OpenningBalanceController;
use Modules\Stock\Http\Controllers\ProfitReportsController;
use Modules\Stock\Http\Controllers\RealizedUnrealizedController;
use Modules\Stock\Http\Controllers\StockController;
use Modules\Stock\Http\Controllers\StockMarketController;
use Modules\Stock\Http\Controllers\StockSalePurchaseDetailController;
use Modules\Stock\Http\Controllers\StockSectorController;

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

Route::middleware('auth:api')->get('/stock', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'stock'], function () {
    Route::controller(StockController::class)->group(function () {
        Route::get('/', 'all')->name('stock.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('stock.create');
        Route::put('/{id}', 'update')->name('stock.update');
        Route::delete('/{id}', 'delete')->name('stock.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'stock-market'], function () {
    Route::controller(StockMarketController::class)->group(function () {
        Route::get('/', 'all')->name('stockMarket.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('stockMarket.create');
        Route::put('/{id}', 'update')->name('stockMarket.update');
        Route::delete('/{id}', 'delete')->name('stockMarket.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'stock-sale-purchase'], function () {
    Route::controller(StockSalePurchaseDetailController::class)->group(function () {
        Route::get('/', 'all')->name('stock-sale-purchase.index');
        Route::get('/{id}', 'find');
        Route::post('/updaterow', 'updaterow')->name('stock-sale-purchase.updaterow');
        Route::post('/updataData', 'updataData')->name('stock-sale-purchase.updataData');
        Route::post('/', 'create')->name('stock-sale-purchase.create');
        Route::put('/{id}', 'update')->name('stock-sale-purchase.update');
        Route::post('/deleteData', 'deleteData')->name('stock-sale-purchase.deleteData');
    });
});

Route::group(['prefix' => 'stock-sector'], function () {
    Route::controller(StockSectorController::class)->group(function () {
        Route::get('/', 'all')->name('stockSector.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('stockSector.create');
        Route::put('/{id}', 'update')->name('stockSector.update');
        Route::delete('/{id}', 'delete')->name('stockSector.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::get("/currency", [\App\Http\Controllers\Currency\CurrencyController::class, "all"])->name('currency.index');

Route::group(['prefix' => 'openning-balance'], function () {
    Route::controller(OpenningBalanceController::class)->group(function () {
        Route::get('/', 'all')->name('openning-balance.index');
        Route::get('/all', 'getAll')->name('openning-balance.getAll');
        Route::get('/{id}', 'find');
        Route::get('checkdate/{id}', 'checkDate')->name('openning-balance.checkdate');
        Route::post('/', 'create')->name('openning-balance.create');
        // Route::put('/', 'update')->name('openning-balance.update');
        Route::post('/updaterow', 'rowUpdate')->name('openning-balance.updaterow');
        Route::delete('/{id}', 'delete')->name('openning-balance.destroy');
        Route::get('/{id}', 'getWalletEntier')->name('openning-balance.getwallet');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});
Route::group(['prefix' => 'closing-balance'], function () {
    Route::controller(ClosingBalanceController::class)->group(function () {
        Route::get('/', 'all')->name('closing-balance.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('closing-balance.create');
        // Route::put('/{id}', 'update')->name('closing-balance.update');
        Route::post('/updaterow', 'rowUpdate')->name('closing-balance.updaterow');
        Route::delete('/destroy/{date}', 'delete')->name('closing-balance.destroy');
        Route::get('/getentier/{date}', 'getAllEntier')->name('closing-balance.getentier');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'all-transactions'], function () {
    Route::controller(AllTransactionController::class)->group(function () {
        Route::post('/', 'all')->name('all-transactions.index');
    });
});

Route::group(['prefix' => 'profit-reports'], function () {
    Route::controller(ProfitReportsController::class)->group(function () {
        Route::post('/', 'all')->name('profit-reports.index');
    });
});
Route::group(['prefix' => 'realized-unrealized'], function () {
    Route::controller(RealizedUnrealizedController::class)->group(function () {
        Route::post('/', 'all')->name('realized-unrealized.index');
    });
});

//customTable
Route::group(['prefix' => 'Stock/CustomTable'], function () {
    Route::controller(\Modules\Stock\Http\Controllers\StockCustomTableController::class)->group(function () {
        Route::get('/', 'all')->name('customTable.index');
        Route::get('/seeder', 'seeder');

        Route::get('/table-columns/{tableName}', 'getCustomTableFields');
        Route::get('logs/{id}', 'logs')->name('customTable.logs');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('customTable.create');
        Route::put('/update', 'update')->name('customTable.update');
        Route::delete('/{id}', 'delete')->name('customTable.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});
