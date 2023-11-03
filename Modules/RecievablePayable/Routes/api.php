<?php

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

Route::middleware('auth:api')->get('/recievablepayable', function (Request $request) {
    return $request->user();
});

Route::prefix('recievable-payable')->group(function () {
//    Route::middleware ('auth:sanctum')->group (function (){
//
//    });


  //customTable
  Route::group(['prefix' => 'CustomTable'], function () {
    Route::controller(\Modules\RecievablePayable\Http\Controllers\RecievablePayableCustomTableController::class)->group(function () {
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

    Route::resource('rp_installment_condation', 'RpInstallmentCondationController')->except('edit', 'create');
    Route::get('rp_installment_condation/logs/{id}', 'RpInstallmentCondationController@logs');
    Route::post('rp_installment_condation/logs/bulk-delete', 'RpInstallmentCondationController@bulkDelete');

    Route::resource('rp_installment_payment_types', 'RpInstallmentPaymentTypeController')->except('edit', 'create');
    Route::get('rp_installment_payment_types/logs/{id}', 'RpInstallmentPaymentTypeController@logs');
    Route::post('rp_installment_payment_types/bulk-delete', 'RpInstallmentPaymentTypeController@bulkDelete');

    Route::resource('rp_installment_status', 'RpInstallmentStatusController')->except('edit', 'create');
    Route::get('rp_installment_status/logs/{id}', 'RpInstallmentStatusController@logs');
    Route::post('rp_installment_status/bulk-delete', 'RpInstallmentStatusController@bulkDelete');

    Route::resource('rp_payment_plan_installment', 'RpPaymentPlanInstallmentController')->except('edit', 'create');
    Route::get('rp_payment_plan_installment/logs/{id}', 'RpPaymentPlanInstallmentController@logs');
    Route::post('rp_payment_plan_installment/bulk-delete', 'RpPaymentPlanInstallmentController@bulkDelete');

    Route::resource('rp_main_contact_group', 'RpMainContactGroupController')->except('edit', 'create');
    Route::get('rp_main_contact_group/logs/{id}', 'RpMainContactGroupController@logs');
    Route::post('rp_main_contact_group/bulk-delete', 'RpMainContactGroupController@bulkDelete');

    Route::resource('rp_sub_contact_group', 'RpSubContactGroupController')->except('edit', 'create');
    Route::get('rp_sub_contact_group/logs/{id}', 'RpSubContactGroupController@logs');
    Route::post('rp_sub_contact_group/bulk-delete', 'RpSubContactGroupController@bulkDelete');

    Route::resource('rp_installment_p_plan', 'RpInstallmentPaymentPlanController')->except('edit', 'create');
    Route::get('rp_installment_p_plan/logs/{id}', 'RpInstallmentPaymentPlanController@logs');
    Route::post('rp_installment_p_plan/bulk-delete', 'RpInstallmentPaymentPlanController@bulkDelete');

    Route::resource('rp_screen_sub_contact_group', 'RpScreenSubContactGroupController')->except('edit', 'create');
    Route::get('rp_screen_sub_contact_group/logs/{id}', 'RpScreenSubContactGroupController@logs');
    Route::post('rp_screen_sub_contact_group/bulk-delete', 'RpScreenSubContactGroupController@bulkDelete');

    Route::resource('rp_document_plan', 'RpDocumentPlanController')->except('edit', 'create');
    Route::get('rp_document_plan/logs/{id}', 'RpDocumentPlanController@logs');
    Route::post('rp_document_plan/logs/bulk-delete', 'RpDocumentPlanController@bulkDelete');

    Route::resource('rp_opening_balance', 'OpeningBalanceController')->except('edit', 'create');
    Route::get('rp_opening_balance/logs/{id}', 'OpeningBalanceController@logs');
    Route::post('rp_opening_balance/logs/bulk-delete', 'OpeningBalanceController@bulkDelete');

    Route::resource('rp_debit_note', 'DebitNoteController')->except('edit', 'create');
    Route::get('rp_debit_note/logs/{id}', 'DebitNoteController@logs');
    Route::post('rp_debit_note/bulk-delete', 'DebitNoteController@bulkDelete');

    Route::resource('rp_break_down', 'RpBreakDownController')->except('edit', 'create');
    Route::get('rp_break_down/logs/{id}', 'RpBreakDownController@logs');
    Route::get('filterBreak', 'RpBreakDownController@filterBreak');
    Route::get('all-break-customer-money-details', 'RpBreakDownController@getBreakCustomerMoneyDetails');
    Route::post('rp_break_down/logs/bulk-delete', 'RpBreakDownController@bulkDelete');
    Route::post('rp_break_down/update_break', 'RpBreakDownController@updateBreak');

    Route::get('filter-vourcher', 'RpReportController@filterVourcher');
    Route::get('customer-statement-of-account', 'RpBreakDownController@getCustomerStatementOfAccount');
    Route::get('document-with-money-details', 'RpBreakDownController@getDocumentWithMoneyDetails');


});
