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

Route::prefix('real-estate')->group(function () {
    // owners routes
    Route::group(['prefix' => 'owners'], function () {
        Route::get('/get-drop-down', 'RlstOwnerController@getDropDown');

        Route::get('/', 'RlstOwnerController@all')->name('rlst-owners.all');
        Route::get('/ownerWalletPercentage/{wallet_id}/{owner_id}', 'RlstOwnerController@ownerWalletPercentage')->name('rlst-owners.ownerWalletPercentage');
        Route::get('/logs/{id}', 'RlstOwnerController@logs')->name('rlst-owners.logs');
        Route::get('/{id}', 'RlstOwnerController@find')->name('rlst-owners.find');
        Route::post('/', 'RlstOwnerController@create')->name('rlst-owners.create');
        Route::put('/{id}', 'RlstOwnerController@update')->name('rlst-owners.update');
        Route::post("/bulk-delete", "RlstOwnerController@bulkDelete");
        Route::delete('/{id}', 'RlstOwnerController@delete')->name('rlst-owners.delete');
    });
    // customers routes
    Route::group(['prefix' => 'customers'], function () {
        Route::get('/', 'RlstCustomerController@all')->name('rlst-customers.all');
        Route::get('/logs/{id}', 'RlstCustomerController@logs')->name('rlst-customers.logs');
        Route::get('/{id}', 'RlstCustomerController@find')->name('rlst-customers.find');
        Route::post('/', 'RlstCustomerController@create')->name('rlst-customers.create');
        Route::put('/{id}', 'RlstCustomerController@update')->name('rlst-customers.update');
        Route::delete("/bulk-delete", "RlstCustomerController@bulkDelete");
        Route::delete('/{id}', 'RlstCustomerController@delete')->name('rlst-customers.delete');
    });

    // wallets routes
    Route::group(['prefix' => 'wallets'], function () {
        Route::get('/get-drop-down', 'RlstWalletController@getDropDown')->name('wallets.getDropDown');

        Route::get('/', 'RlstWalletController@all')->name('rlst-wallets.all');
        Route::get("/bu-ty/{wallet_id}/{building_id}", 'RlstWalletController@buTy');
        Route::get('/logs/{id}', 'RlstWalletController@logs')->name('rlst-wallets.logs');
        Route::get('/{id}', 'RlstWalletController@find')->name('rlst-wallets.find');
        Route::post('/', 'RlstWalletController@create')->name('rlst-wallets.create');
        Route::put('/{id}', 'RlstWalletController@update')->name('rlst-wallets.update');
        Route::post("/bulk-delete", "RlstWalletController@bulkDelete");
        Route::delete('/{id}', 'RlstWalletController@delete')->name('rlst-wallets.delete');
    });

    // wallet-owner

    Route::group(['prefix' => 'wallet-owner'], function () {
        Route::get('/', 'RlstWalletOwnerController@all')->name('rlst-wallet-owner.all');
        Route::get('/logs/{id}', 'RlstWalletOwnerController@logs')->name('rlst-wallet-owner.logs');
        Route::get('/{id}', 'RlstWalletOwnerController@find')->name('rlst-wallet-owner.find');
        Route::post('/', 'RlstWalletOwnerController@create')->name('rlst-wallet-owner.create');
        Route::put('/{id}', 'RlstWalletOwnerController@update')->name('rlst-wallet-owner.update');
        Route::post("/bulk-delete", "RlstWalletOwnerController@bulkDelete");
        Route::delete('/{id}', 'RlstWalletOwnerController@delete')->name('rlst-wallet-owner.delete');
    });

    //installments

    Route::group(['prefix' => 'installments'], function () {
        Route::get('/', 'RlstInstallmentController@all')->name('rlst-installments.all');
        Route::get('/logs/{id}', 'RlstInstallmentController@logs')->name('rlst-installments.logs');
        Route::get('/{id}', 'RlstInstallmentController@find')->name('rlst-installments.find');
        Route::post('/', 'RlstInstallmentController@create')->name('rlst-installments.create');
        Route::put('/{id}', 'RlstInstallmentController@update')->name('rlst-installments.update');
        Route::delete("/bulk-delete", "RlstInstallmentController@bulkDelete");
        Route::delete('/{id}', 'RlstInstallmentController@delete')->name('rlst-installments.delete');
    });

    //reservations
    Route::group(['prefix' => 'reservations'], function () {
        Route::get('/', 'RlstReservationController@all')->name('rlst-Reservations.all');
        Route::get('/logs/{id}', 'RlstReservationController@logs')->name('rlst-Reservations.logs');
        Route::get('/{id}', 'RlstReservationController@find')->name('rlst-Reservations.find');
        Route::post('/', 'RlstReservationController@create')->name('rlst-Reservations.create');
        Route::put('/{id}', 'RlstReservationController@update')->name('rlst-Reservations.update');
        Route::post("/bulk-delete", "RlstReservationController@bulkDelete");
        Route::delete('/{id}', 'RlstReservationController@delete')->name('rlst-Reservations.delete');
        Route::get('/getBranch/{branch_id}', 'RlstReservationController@getSerial')->name('rlst-Reservations.getSerial');
    });
    // reservation units
    Route::group(['prefix' => 'reservation-units'], function () {
        Route::get('/', 'RlstReservationUnitController@all')->name('rlst-reservation-units.all');
        Route::get('/logs/{id}', 'RlstReservationUnitController@logs')->name('rlst-reservation-units.logs');
        Route::get('/{id}', 'RlstReservationUnitController@find')->name('rlst-reservation-units.find');
        Route::post('/', 'RlstReservationUnitController@create')->name('rlst-reservation-units.create');
        Route::put('/{id}', 'RlstReservationUnitController@update')->name('rlst-reservation-units.update');
        Route::delete("/bulk-delete", "RlstReservationUnitController@bulkDelete");
        Route::delete('/{id}', 'RlstReservationUnitController@delete')->name('rlst-reservation-units.delete');
    });

    // contracs

    Route::group(['prefix' => 'contracts'], function () {
        Route::get('/general-filter', 'RlstContractController@generalFilter');

        Route::get('/{unit_id}/{unit_status_id}', 'RlstContractController@getSerialNumber');

        Route::get('/', 'RlstContractController@all')->name('rlst-contracts.all');
        Route::get('/logs/{id}', 'RlstContractController@logs')->name('rlst-contracts.logs');
        Route::get('/{id}', 'RlstContractController@find')->name('rlst-contracts.find');
        Route::post('/', 'RlstContractController@create')->name('rlst-contracts.create');
        Route::put('/{id}', 'RlstContractController@update')->name('rlst-contracts.update');
        Route::post("/bulk-delete", "RlstContractController@bulkDelete");
        Route::delete('/{id}', 'RlstContractController@delete')->name('rlst-contracts.delete');

    });

    //unit contracts
    Route::group(['prefix' => 'unit-contracts'], function () {
        Route::get('/', 'RlstUnitContractController@all')->name('rlst-unit-contracts.all');
        Route::get('/logs/{id}', 'RlstUnitContractController@logs')->name('rlst-unit-contracts.logs');
        Route::get('/{id}', 'RlstUnitContractController@find')->name('rlst-unit-contracts.find');
        Route::post('/', 'RlstUnitContractController@create')->name('rlst-unit-contracts.create');
        Route::put('/{id}', 'RlstUnitContractController@update')->name('rlst-unit-contracts.update');
        Route::post("/bulk-delete", "RlstUnitContractController@bulkDelete");
        Route::delete('/{id}', 'RlstUnitContractController@delete')->name('rlst-unit-contracts.delete');
    });

    // units
    Route::group(['prefix' => 'units'], function () {
        Route::get('/get-drop-down', 'RlstUnitController@getDropDown')->name('rlst-units.getDropDown');

        Route::get('/general-filter', 'RlstUnitController@generalFilter');

        Route::get('/owner-wallet/{wallet_id}', 'RlstUnitController@getOwnerByWalletId');

        Route::post('/building-wallet', 'RlstUnitController@getBuildingByWalletId');

        Route::get('/', 'RlstUnitController@all')->name('rlst-units.all');
        Route::get('/logs/{id}', 'RlstUnitController@logs')->name('rlst-units.logs');
        Route::get('/{id}', 'RlstUnitController@find')->name('rlst-units.find');
        Route::post('/', 'RlstUnitController@create')->name('rlst-units.create');
        Route::put('/{id}', 'RlstUnitController@update')->name('rlst-units.update');
        Route::post("/bulk-delete", "RlstUnitController@bulkDelete");
        Route::delete('/{id}', 'RlstUnitController@delete')->name('rlst-units.delete');

    });

    // unit statuses
    Route::group(['prefix' => 'unit-statuses'], function () {
        Route::get('/get-drop-down', 'RlstUnitStatusController@getDropDown')->name('rlst-unit-statuses.getDropDown');

        Route::get('/', 'RlstUnitStatusController@all')->name('rlst-unit-statuses.all');
        Route::get('/logs/{id}', 'RlstUnitStatusController@logs')->name('rlst-unit-statuses.logs');
        Route::get('/{id}', 'RlstUnitStatusController@find')->name('rlst-unit-statuses.find');
        Route::post('/', 'RlstUnitStatusController@create')->name('rlst-unit-statuses.create');
        Route::put('/{id}', 'RlstUnitStatusController@update')->name('rlst-unit-statuses.update');
        Route::post("/bulk-delete", "RlstUnitStatusController@bulkDelete");
        Route::delete('/{id}', 'RlstUnitStatusController@delete')->name('rlst-unit-statuses.delete');

    });
    // property types
    Route::group(['prefix' => 'property-types'], function () {
        Route::get('/', 'RlstPropertyTypeController@all')->name('rlst-property-types.all');
        Route::get('/logs/{id}', 'RlstPropertyTypeController@logs')->name('rlst-property-types.logs');
        Route::get('/{id}', 'RlstPropertyTypeController@find')->name('rlst-property-types.find');
        Route::post('/', 'RlstPropertyTypeController@create')->name('rlst-property-types.create');
        Route::put('/{id}', 'RlstPropertyTypeController@update')->name('rlst-property-types.update');
        Route::delete("/bulk-delete", "RlstPropertyTypeController@bulkDelete");
        Route::delete('/{id}', 'RlstPropertyTypeController@delete')->name('rlst-property-types.delete');
    });

    // buildings
    Route::group(['prefix' => 'buildings'], function () {
        Route::get('/get-drop-down', 'RlstBuildingController@getDropDown')->name('rlst-buildings.getDropDown');

        Route::get('/', 'RlstBuildingController@all')->name('rlst-buildings.all');
        Route::get('/logs/{id}', 'RlstBuildingController@logs')->name('rlst-buildings.logs');
        Route::get('/{id}', 'RlstBuildingController@find')->name('rlst-buildings.find');
        Route::post('/', 'RlstBuildingController@create')->name('rlst-buildings.create');
        Route::put('/{id}', 'RlstBuildingController@update')->name('rlst-buildings.update');
        Route::post("/bulk-delete", "RlstBuildingController@bulkDelete");
        Route::delete('/{id}', 'RlstBuildingController@delete')->name('rlst-buildings.delete');
    });

    // building wallets

    Route::group(['prefix' => 'building-wallet'], function () {
        Route::get('/', 'RlstBuildingWalletController@all')->name('rlst-building-wallets.all');
        Route::get('/logs/{id}', 'RlstBuildingWalletController@logs')->name('rlst-building-wallets.logs');
        Route::get('/{id}', 'RlstBuildingWalletController@find')->name('rlst-building-wallets.find');
        Route::post('/', 'RlstBuildingWalletController@create')->name('rlst-building-wallets.create');
        Route::put('/{id}', 'RlstBuildingWalletController@update')->name('rlst-building-wallets.update');
        Route::post("/bulk-delete", "RlstBuildingWalletController@bulkDelete");
        Route::delete('/{id}', 'RlstBuildingWalletController@delete')->name('rlst-building-wallets.delete');
    });

    //invoices
    Route::group(['prefix' => 'invoices'], function () {

        Route::get('/getAll', 'RlstInvoiceController@getAll');
        Route::get('/', 'RlstInvoiceController@all')->name('rlst-Invoices.all');
        Route::get('/logs/{id}', 'RlstInvoiceController@logs')->name('rlst-Invoices.logs');
        Route::get('/{id}', 'RlstInvoiceController@find')->name('rlst-Invoices.find');
        Route::post('/', 'RlstInvoiceController@create')->name('rlst-Invoices.create');
        Route::put('/{id}', 'RlstInvoiceController@update')->name('rlst-Invoices.update');
        Route::post("/bulk-delete", "RlstInvoiceController@bulkDelete");
        Route::delete('/{id}', 'RlstInvoiceController@delete')->name('rlst-Invoices.delete');
    });

    //CategoryItem
    Route::group(['prefix' => 'Category-item'], function () {
        Route::get('/root-nodes', 'RlstCategoryItemController@getRootNodes')->name('real-estate.modules.root-nodes');
        Route::get('/child-nodes/{parentId}', 'RlstCategoryItemController@getChildNodes')->name('real-esamodules.child-nodes');
        Route::get('/', 'RlstCategoryItemController@all')->name('rlst-Category-item.all');
        Route::put('/{id}', 'RlstCategoryItemController@update')->name('rlst-Category-item.update');
        Route::get('/logs/{id}', 'RlstCategoryItemController@logs')->name('rlst-Category-item.logs');
        Route::get('/{id}', 'RlstCategoryItemController@find')->name('rlst-Category-item.find');
        Route::post('/', 'RlstCategoryItemController@create')->name('rlst-Category-item.create');
        Route::post("/bulk-delete", "RlstCategoryItemController@bulkDelete");
        Route::delete('/{id}', 'RlstCategoryItemController@delete')->name('rlst-Category-item.delete');
    });

    //Item
    Route::group(['prefix' => 'item'], function () {
        Route::get('/', 'RlstItemController@all')->name('rlst-item.all');
        Route::post('/', 'RlstItemController@create')->name('rlst-item.create');
        Route::put('/{id}', 'RlstItemController@update')->name('rlst-item.update');
        Route::get('/logs/{id}', 'RlstItemController@logs')->name('rlst-item.logs');
        Route::get('/{id}', 'RlstItemController@find')->name('rlst-item.find');
        Route::post("/bulk-delete", "RlstItemController@bulkDelete");
        Route::delete('/{id}', 'RlstItemController@delete')->name('rlst-item.delete');
    });

    //Category-Items
    Route::group(['prefix' => 'Categories-item'], function () {
        Route::get('/', 'RlstCategoriesItemController@all')->name('rlst-Category-items.all');
        Route::put('/{id}', 'RlstCategoriesItemController@update')->name('rlst-Category-items.update');
        Route::get('/logs/{id}', 'RlstCategoriesItemController@logs')->name('rlst-Category-items.logs');
        Route::get('/{id}', 'RlstCategoriesItemController@find')->name('rlst-Category-items.find');
        Route::post('/', 'RlstCategoriesItemController@create')->name('rlst-Category-items.create');
        Route::post("/bulk-delete", "RlstCategoriesItemController@bulkDelete");

        Route::delete('/{category_item_id}/{item_id}', 'RlstCategoriesItemController@delete')->name('rlst-Category-items.delete');
    });

    // Unit Type
    Route::group(['prefix' => 'unit-type'], function () {
        Route::get('/get-drop-down', 'RlstUnitTypeController@getDropDown')->name('rlst-unit-type.getDropDown');

        Route::get('/', 'RlstUnitTypeController@all')->name('rlst-unit-type.all');
        Route::post('/', 'RlstUnitTypeController@create')->name('rlst-unit-type.create');
        Route::put('/{id}', 'RlstUnitTypeController@update')->name('rlst-unit-type.update');
        Route::get('/logs/{id}', 'RlstUnitTypeController@logs')->name('rlst-unit-type.logs');
        Route::get('/{id}', 'RlstUnitTypeController@find')->name('rlst-unit-type.find');
        Route::post("/bulk-delete", "RlstUnitTypeController@bulkDelete");
        Route::delete('/{id}', 'RlstUnitTypeController@delete')->name('rlst-unit-type.delete');
    });

    // Unit Type
    Route::group(['prefix' => 'finishing'], function () {
        Route::get('/get-drop-down', 'RlstFinishingController@getDropDown')->name('rlst-unit-type.getDropDown');

        Route::get('/', 'RlstFinishingController@all')->name('rlst-finishing.all');
        Route::post('/', 'RlstFinishingController@create')->name('rlst-finishing.create');
        Route::put('/{id}', 'RlstFinishingController@update')->name('rlst-finishing.update');
        Route::get('/logs/{id}', 'RlstFinishingController@logs')->name('rlst-finishing.logs');
        Route::get('/{id}', 'RlstFinishingController@find')->name('rlst-finishing.find');
        Route::post("/bulk-delete", "RlstFinishingController@bulkDelete");
        Route::delete('/{id}', 'RlstFinishingController@delete')->name('rlst-finishing.delete');
    });

    // view
    Route::group(['prefix' => 'view'], function () {
        Route::get('/get-drop-down', 'RlstViewController@getDropDown')->name('rlst-unit-type.getDropDown');

        Route::get('/', 'RlstViewController@all')->name('rlst-view.all');
        Route::post('/', 'RlstViewController@create')->name('rlst-view.create');
        Route::put('/{id}', 'RlstViewController@update')->name('rlst-view.update');
        Route::get('/logs/{id}', 'RlstViewController@logs')->name('rlst-view.logs');
        Route::get('/{id}', 'RlstViewController@find')->name('rlst-view.find');
        Route::post("/bulk-delete", "RlstViewController@bulkDelete");
        Route::delete('/{id}', 'RlstViewController@delete')->name('rlst-view.delete');
    });

    //customTable
    Route::group(['prefix' => 'CustomTable'], function () {
        Route::controller(\Modules\RealEstate\Http\Controllers\RealestateCustomTableController::class)->group(function () {
            Route::get('/seeder', 'seeder');
            Route::get('/', 'all')->name('customTable.index');
            Route::get('/table-columns/{tableName}', 'getCustomTableFields');
            Route::get('logs/{id}', 'logs')->name('customTable.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('customTable.create');
            Route::put('/update', 'update')->name('customTable.update');
            Route::delete('/{id}', 'delete')->name('customTable.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });

    // Building Type
    Route::group(['prefix' => 'building-type'], function () {
        //Route::get('/get-drop-down', 'RlstBuildingTypeController@getDropDown')->name('rlst-unit-type.getDropDown');

        Route::get('/', 'RlstBuildingTypeController@all')->name('rlst-building-type.all');
        Route::post('/', 'RlstBuildingTypeController@create')->name('rlst-building-type.create');
        Route::put('/{id}', 'RlstBuildingTypeController@update')->name('rlst-building-type.update');
        Route::get('/logs/{id}', 'RlstBuildingTypeController@logs')->name('rlst-building-type.logs');
        Route::get('/{id}', 'RlstBuildingTypeController@find')->name('rlst-building-type.find');
        Route::post("/bulk-delete", "RlstBuildingTypeController@bulkDelete");
        Route::delete('/{id}', 'RlstBuildingTypeController@delete')->name('rlst-building-type.delete');
    });

    // Building Policy
    Route::group(['prefix' => 'building-policy'], function () {
        //Route::get('/get-drop-down', 'RlstFinishingController@getDropDown')->name('rlst-unit-type.getDropDown');
        Route::get('/divide-owner-company-share', 'RlstBuildingPolicyController@divideOwnerCompanyShare')->name('rlst-building-policy.divideOwnerCompanyShare');

        Route::get('/', 'RlstBuildingPolicyController@all')->name('rlst-building-policy.all');
        Route::post('/', 'RlstBuildingPolicyController@create')->name('rlst-building-policy.create');
        Route::put('/{id}', 'RlstBuildingPolicyController@update')->name('rlst-building-policy.update');
        Route::get('/logs/{id}', 'RlstBuildingPolicyController@logs')->name('rlst-building-policy.logs');
        Route::get('/{id}', 'RlstBuildingPolicyController@find')->name('rlst-building-policy.find');
        Route::post("/bulk-delete", "RlstBuildingPolicyController@bulkDelete");
        Route::delete('/{id}', 'RlstBuildingPolicyController@delete')->name('rlst-building-policy.delete');
    });

    //  Policy
    Route::group(['prefix' => 'policy'], function () {
        //Route::get('/get-drop-down', 'RlstFinishingController@getDropDown')->name('rlst-unit-type.getDropDown');

        Route::get('/', 'RlstPolicyController@all')->name('rlst-policy.all');
        Route::post('/', 'RlstPolicyController@create')->name('rlst-policy.create');
        Route::put('/{id}', 'RlstPolicyController@update')->name('rlst-policy.update');
        Route::get('/logs/{id}', 'RlstPolicyController@logs')->name('rlst-policy.logs');
        Route::get('/{id}', 'RlstPolicyController@find')->name('rlst-policy.find');
        Route::post("/bulk-delete", "RlstPolicyController@bulkDelete");
        Route::delete('/{id}', 'RlstPolicyController@delete')->name('rlst-policy.delete');
    });

    Route::group(['prefix' => 'un-sold-unit-report'], function () {

        Route::post('/', 'RlstUnSoldUnitReportController@all');

    });

});
