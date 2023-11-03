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


Route::prefix('booking')->group(function () {

    Route::group(['prefix' => 'units'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\UnitController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('units.getDropDown');
            Route::get('/get-client-units', 'getClientUnits');
            Route::get('/', 'all')->name('units.index');
            Route::get('/{id}', 'find')->name('units.find');
            Route::post('/', 'create')->name('units.create');
            Route::put('/{id}', 'update')->name('units.update');
            Route::delete('/{id}', 'delete')->name('units.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('units.logs');
        });
    });

    Route::group(['prefix' => 'Settings'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\SettingController::class)->group(function () {
            Route::get('/', 'all')->name('Settings.index');
            Route::put('/update', 'update')->name('Settings.update');

        });
    });

    Route::group(['prefix' => 'floors'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\FloorController::class)->group(function () {
            Route::get('/', 'all')->name('units.index');
            Route::get('/{id}', 'find')->name('units.find');
            Route::post('/', 'create')->name('units.create');
            Route::put('/{id}', 'update')->name('units.update');
            Route::delete('/{id}', 'delete')->name('units.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('units.logs');
        });
    });
    Route::group(['prefix' => 'statistics'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\StatisticsController::class)->group(function () {
            Route::get('/', 'all')->name('units.statistics');

        });
    });
    Route::group(['prefix' => 'filter-rooms'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\GetRoomsController::class)->group(function () {
            Route::get('/', 'all')->name('filter-rooms.all');
        });
    });


    Route::group(['prefix' => 'unit-status'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\UnitStatusController::class)->group(function () {
            Route::get('/', 'all')->name('unit-status.index');
            Route::get('/{id}', 'find')->name('unit-status.find');
            Route::post('/', 'create')->name('unit-status.create');
            Route::put('/{id}', 'update')->name('unit-status.update');
            Route::delete('/{id}', 'delete')->name('unit-status.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('unit-status.logs');
        });
    });


    Route::group(['prefix' => 'report'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\ReportController::class)->group(function () {
            Route::get('/house-keeping', 'houseKeeping');
            Route::get('/check-in', 'checkIn');
        });
    });


});
