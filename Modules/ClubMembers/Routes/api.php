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

// Route::middleware('auth:api')->get('/clubmembers', function (Request $request) {
//     return $request->user();
// });

Route::prefix('club-members')->group(function () {

    //customTable
    Route::group(['prefix' => 'clubMembersCustomTable'], function () {
        Route::controller(\Modules\ClubMembers\Http\Controllers\ClubMembersCustomTableController::class)->group(function () {

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

    // sponsers routes
    Route::group(['prefix' => 'sponsers'], function () {
        Route::get('get-sponsors/{group_id}', 'CmSponserController@getSponsors')->name('cm-sponser.getSponsors');
        Route::get('get-sponsors-with-null-group', 'CmSponserController@getSponsorsNullGroup')->name('cm-sponser.getSponsorsNullGroup');
        Route::get('/root-nodes', 'CmSponserController@getRootNodes')->name('modules.root-nodes');
        Route::get('/child-nodes/{parentId}', 'CmSponserController@getChildNodes')->name('modules.child-nodes');
        Route::get('/', 'CmSponserController@all')->name('cm-sponser.all');
        Route::get('/logs/{id}', 'CmSponserController@logs')->name('cm-sponser.logs');
        Route::get('/{id}', 'CmSponserController@find')->name('cm-sponser.find');
        Route::post('/', 'CmSponserController@create')->name('cm-sponser.create');
        Route::put('/{id}', 'CmSponserController@update')->name('cm-sponser.update');
        Route::post("/bulk-delete", "CmSponserController@bulkDelete");
        Route::delete('/{id}', 'CmSponserController@delete')->name('cm-sponser.delete');
    });

    // members routes
    Route::group(['prefix' => 'members'], function () {
        Route::get('/getMemberForMultiSubscription', 'CmMemberController@getMemberForMultiSubscription')->name('cm-members.getMemberForMultiSubscription');
        Route::get('/report-to-members', 'CmMemberController@reportToMembers')->name('cm-members.report-to-members');
        Route::get('get-sponsors', 'CmMemberController@getSponsors')->name('cm-members.getSponsors');
        Route::get('/report-cm-member', 'CmMemberController@getReportCmMember');
        Route::get('/updateCmMember', 'CmMemberController@getUpdateCmMember');
        Route::get('/updateLastTransactionDate', 'CmMemberController@updateLastTransactionDate');
        Route::get('/update-financial-status-cm-member', 'CmMemberController@getUpdateFinancialStatusCmMember');
        Route::get('/test-transfer', 'CmMemberController@TestTransfer');
        Route::get('/dataMemberTable', 'CmMemberController@dataMemberTable');
        Route::get('/dataMemberFildFullNameTable', 'CmMemberController@dataMemberFildFullNameTable');
        Route::get('/pending', 'CmMemberController@allAcceptancePending')->name('cm-members.allAcceptancePending');
        Route::get('/Acceptance', 'CmMemberController@allAcceptance')->name('cm-members.allAcceptance');
        Route::get('/', 'CmMemberController@all')->name('cm-members.all');
        Route::get('/dataMemberTable', 'CmMemberController@dataMemberTable');

        Route::get('/logs/{id}', 'CmMemberController@logs')->name('cm-members.logs');
        Route::get('/{id}', 'CmMemberController@find')->name('cm-members.find');
        Route::get('public-update-permission-cm-member/{id}', 'CmMemberController@getPublicUpdatePermissionCmMember');
        Route::put('/update-sponsor-members', 'CmMemberController@updateSponsorID');

        Route::post('/', 'CmMemberController@create')->name('cm-members.create');
        Route::put('/{id}', 'CmMemberController@update')->name('cm-members.update');
        Route::post("/bulk-delete", "CmMemberController@bulkDelete");
        Route::delete('/{id}', 'CmMemberController@delete')->name('cm-members.delete');
        Route::put('/accept-member/{id}', 'CmMemberController@acceptMember')->name('cm-members.acceptMember');
        Route::put('/decline-member/{id}', 'CmMemberController@declineMember')->name('cm-members.declineMember');

        Route::put('/sponsor/{sponsor_id}', 'CmMemberController@updateSponsor')->name('cm-members.updateSponsor');

        Route::post('/bulk-update', "CmMemberController@acceptMembers");
        Route::put('/update-accepted-members/{id}', 'CmMemberController@updateAcceptedMembers')->name('cm-members.updateAcceptedMembers');

    });
    // member-requests routes
    Route::group(['prefix' => 'member-requests'], function () {
        Route::get('/checkNationalId', 'CmMemberRequestController@checkNationalId')->name('cm-members.checkNationalId');

        Route::get('/', 'CmMemberRequestController@all')->name('cm-members.all');
        Route::get('/logs/{id}', 'CmMemberRequestController@logs')->name('cm-member-requests.logs');
        Route::get('/{id}', 'CmMemberRequestController@find')->name('cm-member-requests.find');
        Route::post('/', 'CmMemberRequestController@create')->name('cm-member-requests.create');
        Route::put('/{id}', 'CmMemberRequestController@update')->name('cm-member-requests.update');
        Route::post("/bulk-delete", "CmMemberRequestController@bulkDelete");
        Route::delete('/{id}', 'CmMemberRequestController@delete')->name('cm-member-requests.delete');
    });

    // financial status routes
    Route::group(['prefix' => 'financial-status'], function () {

        Route::get('/', 'CmFinancialStatusController@all')->name('financial-status.all');
        Route::get('/logs/{id}', 'CmFinancialStatusController@logs')->name('cm-financial-status.logs');
        Route::get('/{id}', 'CmFinancialStatusController@find')->name('cm-financial-status.find');
        Route::post('/', 'CmFinancialStatusController@create')->name('cm-financial-status.create');
        Route::put('/{id}', 'CmFinancialStatusController@update')->name('cm-financial-status.update');
        Route::post("/bulk-delete", "CmFinancialStatusController@bulkDelete");
        Route::delete('/{id}', 'CmFinancialStatusController@delete')->name('cm-financial-status.delete');
    });

// members-types  routes
    Route::group(['prefix' => 'members-types'], function () {

        Route::get('reject-member-type', 'CmMemberTypeController@getRejectMemberType');
        Route::get('/', 'CmMemberTypeController@all')->name('cm_members_types.all');
        Route::get('/logs/{id}', 'CmMemberTypeController@logs')->name('cm_members_types.logs');
        Route::get('/{id}', 'CmMemberTypeController@find')->name('cm_members_types.find');
        Route::post('/', 'CmMemberTypeController@create')->name('cm_members_types.create');
        Route::put('/{id}', 'CmMemberTypeController@update')->name('cm_members_types.update');
        Route::post("/bulk-delete", "CmMemberTypeController@bulkDelete");
        Route::delete('/{id}', 'CmMemberTypeController@delete')->name('cm_members_types.delete');
    });
// members-permissions  routes
    Route::group(['prefix' => 'members-permissions'], function () {

        Route::get('/', 'CmMemberPermissionController@all')->name('cm_members_permissions.all');
        Route::get('/logs/{id}', 'CmMemberPermissionController@logs')->name('cm_members_permissions.logs');
        Route::get('/{id}', 'CmMemberPermissionController@find')->name('cm_members_permissions.find');
        Route::post('/', 'CmMemberPermissionController@create')->name('cm_members_permissions.create');
        Route::put('/{id}', 'CmMemberPermissionController@update')->name('cm_members_permissions.update');
        Route::post("/bulk-delete", "CmMemberPermissionController@bulkDelete");
        Route::delete('/{id}', 'CmMemberPermissionController@delete')->name('cm_members_permissions.delete');
    });
    // members-types  routes
    Route::group(['prefix' => 'pending-members'], function () {

        Route::get('/', 'CmPendingMemberController@all')->name('cm_pending_members.all');
        Route::get('/logs/{id}', 'CmPendingMemberController@logs')->name('cm_pending_members.logs');
        Route::get('/{id}', 'CmPendingMemberController@find')->name('cm_pending_members.find');
        Route::post('/', 'CmPendingMemberController@create')->name('cm_pending_members.create');
        Route::put('/{id}', 'CmPendingMemberController@update')->name('cm_pending_members.update');
        Route::post("/bulk-delete", "CmPendingMemberController@bulkDelete");
        Route::delete('/{id}', 'CmPendingMemberController@delete')->name('cm_pending_members.delete');
    });

    // memberships-renewals  routes
    Route::group(['prefix' => 'memberships-renewals'], function () {

        Route::get('/', 'CmMembershipRenewalController@all')->name('cm_memberships_renewals.all');
        Route::get('/logs/{id}', 'CmMembershipRenewalController@logs')->name('cm_memberships_renewals.logs');
        Route::get('/{id}', 'CmMembershipRenewalController@find')->name('cm_memberships_renewals.find');
        Route::post('/', 'CmMembershipRenewalController@create')->name('cm_memberships_renewals.create');
        Route::put('/{id}', 'CmMembershipRenewalController@update')->name('cm_memberships_renewals.update');
        Route::post("/bulk-delete", "CmMembershipRenewalController@bulkDelete");
        Route::delete('/{id}', 'CmMembershipRenewalController@delete')->name('cm_memberships_renewals.delete');
    });

    // members-settings  routes
    Route::group(['prefix' => 'members-setting'], function () {

        Route::get('/', 'CmMemberSettingController@all')->name('cm-member-setting.all');
        Route::get('/logs/{id}', 'CmMemberSettingController@logs')->name('cm-member-setting.logs');
        Route::get('/{id}', 'CmMemberSettingController@find')->name('cm-member-setting.find');
        Route::post('/', 'CmMemberSettingController@create')->name('cm-member-setting.create');
        Route::put('/{id}', 'CmMemberSettingController@update')->name('cm-member-setting.update');
        Route::post("/bulk-delete", "CmMemberSettingController@bulkDelete");
        Route::delete('/{id}', 'CmMemberSettingController@delete')->name('cm-member-setting.delete');
    });

    Route::group(['prefix' => 'type-permission'], function () {
        Route::get('/', 'CmTypePermissionController@all')->name('type-permission.all');
        Route::get('/logs/{id}', 'CmTypePermissionController@logs')->name('type-permission.logs');
        Route::get('/{id}', 'CmTypePermissionController@find')->name('type-permission.find');
        Route::post('/', 'CmTypePermissionController@create')->name('type-permission.create');
        Route::put('/{id}', 'CmTypePermissionController@update')->name('type-permission.update');
        Route::post("/bulk-delete", "CmTypePermissionController@bulkDelete");
        Route::delete('/{id}', 'CmTypePermissionController@delete')->name('type-permission.delete');
    });

    Route::group(['prefix' => 'transactions'], function () {

        Route::get('report-cm-transactions', 'CmTransactionController@reportCmTransactions');
        Route::get('report-sponsor-paid-transactions', 'CmTransactionController@reportSponsorPaidTransactions');
        Route::get('unpaid-member-transaction', 'CmTransactionController@unpaidMemberTransaction');
        Route::get('/member-transaction/{id}', 'CmTransactionController@MemberTransactions')
            ->name('MemberTransaction.find');
        Route::get('/member-last-transaction/{id}', 'CmTransactionController@findCmMemberLastTransaction');
        Route::get('check-date-member-transaction', 'CmTransactionController@checkDateMemberTransaction');
        Route::get('member-transaction-paid-after-date', 'CmTransactionController@memberTransactionPaidAfterDate');
        Route::get('get-member-voting', 'CmTransactionController@getMemberVoting');

        Route::get('get-member-is-document', 'CmTransactionController@getMemberIsDocument');

        Route::get('member-transaction-defore-after-date', 'CmTransactionController@memberTransactionBeforeAndAfterDate');

        Route::get('/', 'CmTransactionController@all')->name('transaction.all');
        Route::get('/logs/{id}', 'CmTransactionController@logs')->name('transaction.logs');
        Route::get('/{id}', 'CmTransactionController@find')->name('transaction.find');
        Route::post('/', 'CmTransactionController@create')->name('transaction.create');
        Route::put('/{id}', 'CmTransactionController@update')->name('transactionupdate');
        Route::post("/bulk-delete", "CmTransactionController@bulkDelete");
        Route::delete('/{id}', 'CmTransactionController@delete')->name('transaction.delete');
        Route::post('check-date-member-transaction-update', 'CmTransactionController@UpdateMemberTransactionPaid');
        Route::post('unpaid-member-transaction-update', 'CmTransactionController@updateUnpaidMemberTransaction');
        Route::post('member-transaction-paid-after-date-update', 'CmTransactionController@UpdateMemberTransactionPaidAfterDate');
        Route::put('update-member-voting', 'CmTransactionController@updateMemberVoting');


    });

    Route::group(['prefix' => 'discharge-resons'], function () {

        Route::get('/', 'CmDischargeResonController@index');

    });

    Route::group(['prefix' => 'member-status'], function () {

        Route::get('/', 'CmNewMemberStatusController@index');

    });

    Route::group(['prefix' => 'statics'], function () {

        Route::get('/getStatics', 'CmStaticsController@getStatics')
            ->name('statics.getStatics');


        Route::get('/get-members-valid', 'CmStaticsController@validMembers')
            ->name('statics.validMembers');


        Route::get('/get-sponsor-statics', 'CmStaticsController@sponsorStatics')
            ->name('statics.sponsorStatics');


    });

    Route::group(['prefix' => 'rejects'], function () {
        Route::get('/', 'CmMemberRejectController@index')->name('reject.all');
        Route::post('/', 'CmMemberRejectController@create')->name('reject.create');
        Route::get('/logs/{id}', 'CmMemberRejectController@logs')->name('reject.logs');

    });

    // financial status routes
    Route::group(['prefix' => 'sponsor-group'], function () {

        Route::get('/', 'CmSponsorGroupController@all')->name('sponsor-group.all');
        Route::get('/logs/{id}', 'CmSponsorGroupController@logs')->name('cm-sponsor-group.logs');
        Route::get('/{id}', 'CmSponsorGroupController@find')->name('cm-sponsor-group.find');
        Route::post('/', 'CmSponsorGroupController@create')->name('cm-sponsor-group.create');
        Route::put('/{id}', 'CmSponsorGroupController@update')->name('cm-sponsor-group.update');
        Route::post("/bulk-delete", "CmSponsorGroupController@bulkDelete");
        Route::delete('/{id}', 'CmSponsorGroupController@delete')->name('cm-sponsor-group.delete');
    });

    // status routes
    Route::group(['prefix' => 'cm-status'], function () {

        Route::get('/', 'CmMemberStatusController@all')->name('cm-status.all');
        Route::get('/logs/{id}', 'CmMemberStatusController@logs')->name('cm-status.logs');
        Route::get('/{id}', 'CmMemberStatusController@find')->name('cm-status.find');
        Route::post('/', 'CmMemberStatusController@create')->name('cm-status.create');
        Route::put('/{id}', 'CmMemberStatusController@update')->name('cm-status.update');
        Route::post("/bulk-delete", "CmMemberStatusController@bulkDelete");
        Route::delete('/{id}', 'CmMemberStatusController@delete')->name('cm-status.delete');
    });

    // old members report
    Route::group(['prefix' => 'cm-old-members'], function () {

        Route::get('/getOldStatus', 'CmOldMembersReportController@getStatus')->name('cm-old-status.all');
        Route::get('/getOldMembers', 'CmOldMembersReportController@getOldMembers')->name('cm-old-status.all');

    });
});
