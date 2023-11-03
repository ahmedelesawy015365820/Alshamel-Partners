<?php

use App\Http\Controllers\Branch\BranchController;
use App\Http\Controllers\City\CityController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Currency\CurrencyController;
use App\Http\Controllers\GeneralCustomer\GeneralCustomerController;
use App\Http\Controllers\InternalSalesman\InternalSalesmanController;
use App\Http\Controllers\RoleType\RoleTypeController;
use App\Http\Controllers\RoleWorkflow\RoleWorkflowController;
use App\Http\Controllers\ScreenTreeProperty\ScreenTreePropertyController;
use App\Http\Controllers\Serials\SerialController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\TreeProperty\TreePropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['prefix' => 'users'], function () {
    Route::controller(\App\Http\Controllers\User\UserController::class)->group(function () {
        Route::post("login", 'login');
        Route::get("test", 'ahmed');
    });
});

Route::middleware(['authorize.user'])->group(function () {
// Route::get('/backup', function () {
//     Artisan::call('backup:run');
// });

    Route::controller(\App\Http\Controllers\MainController::class)->group(function () {
        Route::post("/media", "media");

        Route::post("/import", "import");
        Route::post("/media-name", "mediaName");
        Route::get("statices", "statices");
        Route::get("data-types", 'dataTypes');
        Route::put("/setting", "setting");
        Route::get("/setting/{user_id}/{screen_id}", "getSetting");
        Route::post("/send-email", "sendEmail");
    });

    // start Notification
    Route::controller(\App\Http\Controllers\MainController::class)->group(function () {
        Route::get('getAllNot', 'getAllNot');
        Route::get('getNotNotRead', 'getNotNotRead');
        Route::post('clearItem/{id}', 'clearItem');
        Route::post('getNotNotRead', 'clearAll');
    });
    // end Notification

    Route::group(['prefix' => 'companies'], function () {
        Route::get('', [CompanyController::class, "index"]);
        Route::get('/{id}', [CompanyController::class, "show"]);
        Route::post('', [CompanyController::class, "store"]);
        Route::post('/{id}', [CompanyController::class, "update"]);
        Route::delete('/{id}', [CompanyController::class, "destroy"]);
    });

    Route::group(['prefix' => 'modules'], function () {
        Route::controller(\App\Http\Controllers\Module\ModuleController::class)->group(function () {
            Route::get('/', 'all')->name('modules.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('modules.create');
            Route::put('/{id}', 'update')->name('modules.update');
            Route::delete('/{id}', 'delete')->name('modules.destroy');
            // Route::post('/{module_id}/company/{company_id}', 'addModuleToCompany')->name('modules.company.add');
            // Route::delete('/{module_id}/company/{company_id}', 'removeModuleFromCompany')->name('modules.company.remove');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/moduleDisable', 'moduleDisable');
        });
    });

    Route::group(['prefix' => 'stores'], function () {
        Route::controller(\App\Http\Controllers\Store\StoreController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('stores.getDropDown');
            Route::get('/', 'all')->name('stores.index');
            Route::get('logs/{id}', 'logs')->name('stores.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('stores.create');
            Route::put('/{id}', 'update')->name('stores.update');
            Route::delete('/{id}', 'delete')->name('stores.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'countries'], function () {

        Route::controller(\App\Http\Controllers\Country\CountryController::class)->group(function () {

            Route::get('/get-drop-down', 'getDropDown')->name('countries.getDropDown');
            Route::get('/', 'all')->name('countries.index');
            Route::get('seeder', 'getCountrySeeder');
            Route::get('logs/{id}', 'logs')->name('countries.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('countries.create');
            Route::put('/{id}', 'update')->name('countries.update');
            Route::delete('/{id}', 'delete')->name('countries.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });

    Route::group(['prefix' => 'governorates'], function () {
        Route::controller(\App\Http\Controllers\Governorate\GovernorateController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('governorates.getDropDown');
            Route::get('/', 'all')->name('governorates.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('governorates.create');
            Route::put('/{id}', 'update')->name('governorates.update');
            Route::delete('/{id}', 'delete')->name('governorates.destroy');
            Route::get('logs/{id}', 'logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'streets'], function () {
        Route::controller(\App\Http\Controllers\StreetController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('streets.getDropDown');
            Route::get('/', 'all')->name('streets.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('streets.create');
            Route::put('/{id}', 'update')->name('streets.update');
            Route::delete('/{id}', 'delete')->name('streets.destroy');
            Route::get('logs/{id}', 'logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

// customer branches
    Route::group(['prefix' => 'customer-branches'], function () {
        Route::controller(\App\Http\Controllers\CustomerBranchController::class)->group(function () {
            Route::get('/', 'all')->name('customer-branches.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('customer-branches.create');
            Route::put('/{id}', 'update')->name('customer-branches.update');
            Route::delete('/{id}', 'delete')->name('customer-branches.destroy');
            Route::get('logs/{id}', 'logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

// contacts routes
    Route::group(['prefix' => 'contacts'], function () {
        Route::controller(\App\Http\Controllers\ContactController::class)->group(function () {
            Route::get('/', 'all')->name('contacts.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('contacts.create');
            Route::put('/{id}', 'update')->name('contacts.update');
            Route::delete('/{id}', 'delete')->name('contacts.destroy');
            Route::get('logs/{id}', 'logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'employees'], function () {
        Route::controller(\App\Http\Controllers\Employee\EmployeeController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('employees.getDropDown');
            Route::get('logs/{id}', 'logs')->name('employees.logs');
            Route::get('/', 'all')->name('employees.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('employees.create');
            Route::put('/{id}', 'update')->name('employees.update');
            Route::delete('/{id}', 'delete')->name('employees.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/hr-employees', 'processJsonData');
        });
    });

    Route::group(['prefix' => 'customTable'], function () {
        Route::controller(\App\Http\Controllers\CustomTable\GeneralCustomTableController::class)->group(function () {
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

    Route::group(['prefix' => 'document'], function () {
        Route::controller(\App\Http\Controllers\Document\DocumentController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('document.getDropDown');
            Route::get('/all-document-money', 'getDocumentMoney')->name('');
            Route::get('/', 'all')->name('document.index');
            Route::get('logs/{id}', 'logs')->name('document.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('document.create');
            Route::put('/{id}', 'update')->name('document.update');
            Route::delete('/{id}', 'delete')->name('document.destroy');
            Route::post("bulk-delete", "bulkDelete");

            Route::post('from_admin', 'createFromAdmin')->name('document.create_from.admin');
        });
    });

    Route::group(['prefix' => 'depertments'], function () {
        Route::controller(\App\Http\Controllers\Depertment\DepertmentController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('depertments.getDropDown');
            Route::get('/', 'all')->name('depertments.index');
            Route::get('logs/{id}', 'logs')->name('depertments.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('depertments.create');
            Route::put('/{id}', 'update')->name('depertments.update');
            Route::delete('/{id}', 'delete')->name('depertments.destroy');
            Route::post("bulk-delete", "bulkDelete");

            Route::post('from_admin', 'createFromAdmin')->name('document.create.from.admin');

            Route::post('/hr-depertments', 'processJsonData');
        });
    });

    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/get-drop-down', '\App\Http\Controllers\TaskController@getDropDown')->name('tasks.getDropDown');
        Route::post('/all', '\App\Http\Controllers\TaskController@allPost')->name('tasks.all.post');
        Route::get('/', '\App\Http\Controllers\TaskController@all')->name('tasks.all');
        Route::post('/', '\App\Http\Controllers\TaskController@create')->name('tasks.create');
        Route::put('/{id}', '\App\Http\Controllers\TaskController@update')->name('tasks.update');
        Route::delete("/bulk-delete", "\App\Http\Controllers\TaskController@bulkDelete");
        Route::get('/logs/{id}', '\App\Http\Controllers\TaskController@logs')->name('tasks.logs');
        Route::get('/{id}', '\App\Http\Controllers\TaskController@find')->name('tasks.find');
        Route::delete('/{id}', '\App\Http\Controllers\TaskController@delete')->name('tasks.delete');
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::controller(\App\Http\Controllers\Transaction\TransactionController::class)->group(function () {
            Route::get('/', 'all')->name('transactions.index');
            Route::get('logs/{id}', 'logs')->name('transactions.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('transactions.create');
            Route::put('/{id}', 'update')->name('transactions.update');
            Route::delete('/{id}', 'delete')->name('transactions.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'messages'], function () {
        Route::controller(\App\Http\Controllers\MessageController::class)->group(function () {
            Route::get('/', 'all')->name('messages.index');
            Route::get('logs/{id}', 'logs')->name('messages.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('messages.create');
            Route::put('/{id}', 'update')->name('messages.update');
            Route::delete('/{id}', 'delete')->name('messages.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::post("send-message", "sendHRMessage");

        });
    });
    Route::group(['prefix' => 'messages-var'], function () {
        Route::controller(\App\Http\Controllers\MessageVarController::class)->group(function () {
            Route::get('/', 'all')->name('messages-var.index');
            Route::get('/{id}', 'find');

        });
    });

    Route::group(['prefix' => 'messages-receiver-contacts'], function () {
        Route::controller(\App\Http\Controllers\MessageReceiverContactController::class)->group(function () {
            Route::get('/', 'all')->name('messages-receiver-contacts.index');
            Route::get('logs/{id}', 'logs')->name('messages-receiver-contacts.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('messages-receiver-contacts.create');
            Route::put('/{id}', 'update')->name('messages-receiver-contacts.update');
            Route::delete('/{id}', 'delete')->name('messages-receiver-contacts.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });

    Route::group(['prefix' => 'message-types'], function () {
        Route::controller(\App\Http\Controllers\MessageTypeController::class)->group(function () {
            Route::get('/', 'all')->name('message-types.index');
            Route::get('logs/{id}', 'logs')->name('message-types.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('message-types.create');
            Route::put('/{id}', 'update')->name('message-types.update');
            Route::delete('/{id}', 'delete')->name('message-types.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });
    Route::group(['prefix' => 'Lawyers'], function () {
        Route::controller(\App\Http\Controllers\LawyerController::class)->group(function () {
            Route::get('/', 'all')->name('Lawyers.index');
            Route::get('logs/{id}', 'logs')->name('Lawyers.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('Lawyers.create');
            Route::put('/{id}', 'update')->name('Lawyers.update');
            Route::delete('/{id}', 'delete')->name('Lawyers.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });
    Route::group(['prefix' => 'legal-procedures'], function () {
        Route::controller(\App\Http\Controllers\LegalProceduresController::class)->group(function () {
            Route::get('/', 'all')->name('legal-procedures.index');
            Route::get('logs/{id}', 'logs')->name('legal-procedures.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('legal-procedures.create');
            Route::put('/{id}', 'update')->name('legal-procedures.update');
            Route::delete('/{id}', 'delete')->name('legal-procedures.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });

    Route::group(['prefix' => 'filter-calender'], function () {
        Route::controller(\App\Http\Controllers\FilterCalenderController::class)->group(function () {
            Route::get('/get-rooms-floors', 'getRoomsByFloors');
            Route::post('/', 'getRoomsCalender');
            Route::post('/get-rooms-calender-filter', 'getRoomsCalenderFilter');
        });
    });

    Route::group(['prefix' => 'statuses'], function () {
        Route::controller(\App\Http\Controllers\Status\StatusController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('statuses.getDropDown');
            Route::get('/get-drop-down-calender', 'getDropDownCalender')->name('statuses.getDropDownCalender');

            Route::get('/', 'all')->name('statuses.index');
            Route::get('logs/{id}', 'logs')->name('statuses.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('statuses.create');
            Route::put('/{id}', 'update')->name('statuses.update');
            Route::delete('/{id}', 'delete')->name('statuses.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('from_admin', 'createFromAdmin')->name('document.create_from_admin');
        });
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::controller(\App\Http\Controllers\CategoryController::class)->group(function () {
            Route::get('/', 'all')->name('categories.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('categories.create');
            Route::put('/{id}', 'update')->name('categories.update');
            Route::delete('/{id}', 'delete')->name('categories.destroy');
            Route::get('logs/{id}', 'logs')->name('categories.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'equipments'], function () {
        Route::controller(\App\Http\Controllers\EquipmentController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('equipments.getDropDown');
            Route::get('root-nodes', 'getRootNodes');
            Route::get('/child-nodes/{parentId}', 'getChildNodes');
            Route::get('/', 'all')->name('equipments.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('equipments.create');
            Route::put('/{id}', 'update')->name('equipments.update');
            Route::delete('/{id}', 'delete')->name('equipments.destroy');
            Route::get('logs/{id}', 'logs')->name('equipments.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'priorities'], function () {
        Route::controller(\App\Http\Controllers\PriorityController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('priorities.getDropDown');
            Route::get('root-nodes', 'getRootNodes');
            Route::get('/child-nodes/{parentId}', 'getChildNodes');
            Route::get('/', 'all')->name('priorities.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('priorities.create');
            Route::put('/{id}', 'update')->name('priorities.update');
            Route::delete('/{id}', 'delete')->name('priorities.destroy');
            Route::get('logs/{id}', 'logs')->name('priorities.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'periodic-maintenances'], function () {
        Route::controller(\App\Http\Controllers\PeriodicMaintenanceController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('periodic-maintenances.getDropDown');
            Route::get('/', 'all')->name('periodic-maintenances.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('periodic-maintenances.create');
            Route::put('/{id}', 'update')->name('periodic-maintenances.update');
            Route::delete('/{id}', 'delete')->name('periodic-maintenances.destroy');
            Route::get('logs/{id}', 'logs')->name('periodic-maintenances.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'locations'], function () {
        Route::controller(\App\Http\Controllers\LocationController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('locations.getDropDown');
            Route::get('root-nodes', 'getRootNodes');
            Route::get('/child-nodes/{parentId}', 'getChildNodes');
            Route::get('/', 'all')->name('locations.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('locations.create');
            Route::put('/{id}', 'update')->name('locations.update');
            Route::delete('/{id}', 'delete')->name('locations.destroy');
            Route::get('logs/{id}', 'logs')->name('locations.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'salesman-plan-source'], function () {
        Route::controller(\App\Http\Controllers\SalesmanPlansSourceController::class)->group(function () {
            Route::get('/', 'all')->name('salesman-plan-source.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('salesman-plan-source.create');
            Route::put('/{id}', 'update')->name('salesman-plan-source.update');
            Route::delete('/{id}', 'delete')->name('salesman-plan-source.destroy');
            Route::get('logs/{id}', 'logs')->name('salesman-plan-source.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });
// general_salesmen_plans

    Route::group(['prefix' => 'financial-years'], function () {
        Route::controller(\App\Http\Controllers\FinancialYear\FinancialYearController::class)->group(function () {
            Route::get('DataOfModelFinancialYear', 'DataOfModelFinancialYear');
            Route::get('/', 'all')->name('financial-years.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('financial-years.create');
            Route::put('/{id}', 'update')->name('financial-years.update');
            Route::delete('/{id}', 'delete')->name('financial-years.destroy');
            Route::get('logs/{id}', 'logs')->name('financial-years.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'units'], function () {
        Route::controller(\App\Http\Controllers\Unit\UnitController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('units.getDropDown');
            Route::get('/', 'all')->name('units.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('units.create');
            Route::put('/{id}', 'update')->name('units.update');
            Route::delete('/{id}', 'delete')->name('units.destroy');
            Route::get('logs/{id}', 'logs')->name('units.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'role-workflows'], function () {
        Route::controller(RoleWorkflowController::class)->group(function () {
            Route::get('/', 'index')->name('role-workflows.index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store')->name('role-workflows.store');
            Route::put('/{id}', 'update')->name('role-workflows.update');
            Route::delete('/{id}', 'destroy')->name('role-workflows.destroy');
            Route::get('logs/{id}', 'logs')->name('role-workflows.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'avenues'], function () {
        Route::controller(\App\Http\Controllers\Avenue\AvenueController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('avenues.getDropDown');
            Route::get('/', 'all')->name('avenues.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('avenues.create');
            Route::put('/{id}', 'update')->name('avenues.update');
            Route::delete('/{id}', 'delete')->name('avenues.destroy');
            Route::get('logs/{id}', 'logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'colors'], function () {
        Route::controller(\App\Http\Controllers\Color\ColorController::class)->group(function () {
            Route::get('/', 'all')->name('colors.index');
            Route::get('logs/{id}', 'logs')->name('colors.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('colors.create');
            Route::put('/{id}', 'update')->name('colors.update');
            Route::delete('/{id}', 'delete')->name('colors.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'salesmen-types'], function () {
        Route::controller(\App\Http\Controllers\SalesmenType\SalesmenTypeController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('salesmen-types.getDropDown');
            Route::get('/', 'all')->name('salesmen-types.index');
            Route::get('logs/{id}', 'logs')->name('salesmen-types.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('salesmen-types.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::put('/{id}', 'update')->name('salesmen-types.update');
            Route::delete('/{id}', 'delete')->name('salesmen-types.destroy');
        });
    });

    Route::group(['prefix' => 'external-salesmen'], function () {
        Route::controller(\App\Http\Controllers\ExternalSalesmen\ExternalSalesmenController::class)->group(function () {
            Route::get('/', 'all')->name('external-salesmen.index');
            Route::get('logs/{id}', 'logs')->name('external-salesmen.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('external-salesmen.create');
            Route::put('/{id}', 'update')->name('external-salesmen.update');
            Route::delete('/{id}', 'delete')->name('external-salesmen.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'payment-types'], function () {
        Route::controller(\App\Http\Controllers\PaymentType\PaymentTypeController::class)->group(function () {
            Route::get('/', 'all')->name('payment-types.index');
            Route::get('logs/{id}', 'logs')->name('payment-types.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('payment-types.create');
            Route::put('/{id}', 'update')->name('payment-types.update');
            Route::delete('/{id}', 'delete')->name('payment-types.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });
    Route::group(['prefix' => 'payment-methods'], function () {
        Route::controller(\App\Http\Controllers\PaymentMethod\PaymentMethodController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('payment-methods.getDropDown');
            Route::get('/', 'all')->name('payment-methods.index');
            Route::get('logs/{id}', 'logs')->name('payment-methods.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('payment-methods.create');
            Route::put('/{id}', 'update')->name('payment-methods.update');
            Route::delete('/{id}', 'delete')->name('payment-methods.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'attendant'], function () {
        Route::controller(\App\Http\Controllers\AttendantController::class)->group(function () {
            Route::get('/', 'all')->name('attendant.index');
            Route::get('/get-drop-down', 'getDropDown')->name('attendant.getDropDown');
            Route::get('logs/{id}', 'logs')->name('attendant.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('attendant.create');
            Route::put('/{id}', 'update')->name('attendant.update');
            Route::delete('/{id}', 'delete')->name('attendant.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'users'], function () {
        Route::controller(\App\Http\Controllers\User\UserController::class)->group(function () {
//            Route::get('/get-drop-down', 'getDropDown')->name('users.getDropDown');
            Route::get('/profile', 'profile');
            Route::get('/', 'all')->name('users.index');
            Route::get('logs/{id}', 'logs')->name('users.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('users.create');
            Route::put('/{id}', 'update')->name('users.update');
            Route::delete('/{id}', 'delete')->name('users.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'user-role'], function () {
        Route::controller(\App\Http\Controllers\UserRole\UserRoleController::class)->group(function () {
            Route::get('/', 'all')->name('user.role.index');
            Route::get('logs/{id}', 'logs')->name('user.role.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('user.role.create');
            Route::put('/{id}', 'update')->name('user.role.update');
            Route::delete('/{id}', 'delete')->name('user.role.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get("user-count/{roleId}", "getRoleUsersCount");
        });
    });

    Route::group(['prefix' => 'banks'], function () {
        Route::controller(\App\Http\Controllers\Bank\BankController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('banks.getDropDown');
            Route::get('/', 'all')->name('banks.index');
            Route::get('logs/{id}', 'logs')->name('banks.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('banks.create');
            Route::put('/{id}', 'update')->name('banks.update');
            Route::delete('/{id}', 'delete')->name('banks.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'bank-accounts'], function () {
        Route::controller(\App\Http\Controllers\BankAccount\BankAccountController::class)->group(function () {
            Route::get('/', 'all')->name('bank-accounts.index');
            Route::get('logs/{id}', 'logs')->name('bank-accounts.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('bank-accounts.create');
            Route::put('/{id}', 'update')->name('bank-accounts.update');
            Route::delete('/{id}', 'delete')->name('bank-accounts.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'role-screen-hotfield'], function () {
        Route::controller(\App\Http\Controllers\RoleScreenHotfield\RoleScreenHotfieldController::class)->group(function () {
            Route::get('/', 'all')->name('role-screen-hotfield.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('role-screen-hotfield.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/{id}', 'update')->name('role-screen-hotfield.update');
            Route::delete('/{id}', 'delete')->name('role-screen-hotfield.destroy');
            Route::get('logs/{id}', 'logs')->name('role-screen-hotfield.logs');
        });
    });

    Route::group(['prefix' => 'role-screen-hotfield'], function () {
        Route::controller(\App\Http\Controllers\RoleScreenHotfield\RoleScreenHotfieldController::class)->group(function () {
            Route::get('/', 'all')->name('role-screen-hotfield.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('role-screen-hotfield.create');
            Route::post('/{id}', 'update')->name('role-screen-hotfield.update');
            Route::delete('/{id}', 'delete')->name('role-screen-hotfield.destroy');
            Route::get('logs/{id}', 'logs')->name('role-screen-hotfield.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'role-workflow-button'], function () {
        Route::controller(\App\Http\Controllers\RoleWorkflowButton\RoleWorkflowButtonController::class)->group(function () {
            Route::get('/', 'all')->name('role-workflow-button.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('role-workflow-button.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/{id}', 'update')->name('role-workflow-button.update');
            Route::delete('/{id}', 'delete')->name('role-workflow-button.destroy');
            Route::get('logs/{id}', 'logs')->name('role-workflow-button.logs');
        });
    });

    Route::group(['prefix' => 'workflow-hotfield'], function () {
        Route::controller(\App\Http\Controllers\WorkflowHotfield\WorkflowHotfieldController::class)->group(function () {
            Route::get('/', 'all')->name('workflow-hotfield.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('workflow-hotfield.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/{id}', 'update')->name('workflow-hotfield.update');
            Route::delete('/{id}', 'delete')->name('workflow-hotfield.destroy');
            Route::get('logs/{id}', 'logs')->name('workflow-hotfield.logs');
        });
    });

    Route::group(['prefix' => 'salesmen'], function () {
        Route::controller(\App\Http\Controllers\Salesman\SalesmanController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('salesmen.getDropDown');
            Route::get('/', 'all')->name('salesmen.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('salesmen.create');
            Route::put('/{id}', 'update')->name('salesmen.update');
            Route::delete('/{id}', 'delete')->name('salesmen.destroy');
            Route::get('logs/{id}', 'logs')->name('salesmen.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'internal-salesmen'], function () {
        Route::controller(\App\Http\Controllers\InternalSalesman\InternalSalesmanController::class)->group(function () {
            Route::get('/', 'all')->name('internal-salesmen.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('internal-salesmen.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/{id}', 'update')->name('internal-salesmen.update');
            Route::delete('/{id}', 'delete')->name('internal-salesmen.destroy');
            Route::get('logs/{id}', 'logs')->name('internal-salesmen.logs');
        });
    });

    Route::group(['prefix' => 'department-tasks'], function () {
        Route::controller(\App\Http\Controllers\DepertmentTask\DepertmentTaskController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('department-task.getDropDown');
            Route::get('/', 'all')->name('department-task.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('department-task.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::put('/{id}', 'update')->name('department-task.update');
            Route::delete('/{id}', 'delete')->name('department-task.destroy');
            Route::get('logs/{id}', 'logs')->name('department-task.logs');
        });
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::controller(\App\Http\Controllers\RoleController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('roles.getDropDown');
            Route::get('/', 'all')->name('roles.index');
            Route::get("/permissions", 'permissions');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('roles.create');
            Route::post("bulk-delete", "bulkDelete");
            Route::put('/{id}', 'update')->name('roles.update');
            Route::delete('/{id}', 'delete')->name('roles.destroy');
            Route::get('logs/{id}', 'logs')->name('roles.logs');

        });
    });

    Route::group(['prefix' => 'branches'], function () {
        Route::controller(BranchController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('branches.getDropDown');
            Route::get('logs/{id}', 'logs')->name('branches.logs');
            Route::post("bulk-delete", "bulkDelete");
            Route::post('/hr-branches', 'processJsonData');
        });
    });

    Route::group(['prefix' => 'role_types'], function () {
        Route::controller(RoleTypeController::class)->group(function () {
            Route::get('logs/{id}', 'logs')->name('role_types.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'serials'], function () {
        Route::controller(SerialController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('serials.getDropDown');
            Route::get('logs/{id}', 'logs')->name('serials.logs');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'restart-period'], function () {
        Route::controller(\App\Http\Controllers\RestartPeriod\RestartPeriodController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('restart-period.getDropDown');
            Route::get('/get-Restart-period-data', 'getRestartPeriodData');
            Route::get('/get-Restart-period-in-serial', 'getRestartPeriodInSerial');
            Route::get('logs/{id}', 'logs')->name('restart-period.logs');
            Route::get('/', 'all')->name('restart-period.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('restart-period.create');
            Route::put('/{id}', 'update')->name('restart-period.update');
            Route::delete('/{id}', 'delete')->name('restart-period.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'salesmen-plans'], function () {
        Route::controller(\App\Http\Controllers\SalesmenPlanController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('salesmen-plans.getDropDown');
            Route::get('logs/{id}', 'logs')->name('salesmen-plans.logs');
            Route::get('/', 'all')->name('salesmen-plans.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('salesmen-plans.create');
            Route::put('/{id}', 'update')->name('salesmen-plans.update');
            Route::delete('/{id}', 'delete')->name('salesmen-plans.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });
    Route::group(['prefix' => 'salesmen-plans-source'], function () {
        Route::controller(\App\Http\Controllers\SalesmenPlansSource\SalesmenPlansSourceController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('salesmen-plans-source.getDropDown');
            Route::get('logs/{id}', 'logs')->name('salesmen-plans-source.logs');
            Route::get('/', 'all')->name('salesmen-plans-source.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('salesmen-plans-source.create');
            Route::put('/{id}', 'update')->name('salesmen-plans-source.update');
            Route::delete('/{id}', 'delete')->name('salesmen-plans-source.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'salesmen-plans-details'], function () {
        Route::controller(\App\Http\Controllers\SalesmenPlansDetailController::class)->group(function () {
            Route::get('logs/{id}', 'logs')->name('salesmen-plans-details.logs');
            Route::get('/', 'all')->name('salesmen-plans-details.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('salesmen-plans-details.create');
            Route::put('/{id}', 'update')->name('salesmen-plans-details.update');
            Route::delete('/{id}', 'delete')->name('salesmen-plans-details.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'document-approval-details'], function () {
        Route::controller(\App\Http\Controllers\DocumentApprovalDetail\DocumentApprovalDetailController::class)->group(function () {
            Route::get('logs/{id}', 'logs')->name('document-approval-details.logs');
            Route::get('/', 'all')->name('document-approval-details.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('document-approval-details.create');
            Route::put('/{id}', 'update')->name('document-approval-details.update');
            Route::delete('/{id}', 'delete')->name('document-approval-details.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });
    Route::group(['prefix' => 'document-statuses'], function () {
        Route::controller(\App\Http\Controllers\DocumentStatuse\DocumentStatuseController::class)->group(function () {
            Route::get('logs/{id}', 'logs')->name('document-statuses.logs');
            Route::get('/', 'all')->name('document-statuses.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('document-statuses.create');
            Route::put('/{id}', 'update')->name('document-statuses.update');
            Route::delete('/{id}', 'delete')->name('document-statuses.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'document-headers'], function () {
        Route::controller(\App\Http\Controllers\DocumentHeader\DocumentHeaderController::class)->group(function () {
            Route::get("check-booking", "checkBooking");
            Route::get("customer-room", "getCustomerRoom");
            Route::get("check-in-customer", "getCheckInCustomer");
            Route::get("update-check-in-customer", "updateCheckInCustomer");
            Route::get('check-out-print/{id}', 'getCheckOutPrint');
            Route::get('logs/{id}', 'logs')->name('document-headers.logs');
            Route::get('all-document-header', 'allDocumentHeader');
            Route::get('check-date', 'checkDateModelFinancialYear');
            Route::get('check-related-document', 'getDateRelatedDocumentId');
            Route::get('/', 'all')->name('document-headers.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('document-headers.create');
            Route::put('/{id}', 'update')->name('document-headers.update');
            Route::delete('/{id}', 'delete')->name('document-headers.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('document-customer/{id}', 'getDocumentsCustomer');
        });
    });

    Route::group(['prefix' => 'item-break-downs'], function () {
        Route::controller(\App\Http\Controllers\ItemBreakDown\ItemBreakDownController::class)->group(function () {
            Route::get('logs/{id}', 'logs')->name('item-break-downs.logs');
            Route::get('/', 'all')->name('item-break-downs.index');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('item-break-downs.create');
            Route::put('/{id}', 'update')->name('item-break-downs.update');
            Route::delete('/{id}', 'delete')->name('item-break-downs.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });

    Route::group(['prefix' => 'document-header-details'], function () {
        Route::controller(\App\Http\Controllers\DocumentHeaderDetail\DocumentHeaderDetailController::class)->group(function () {
            Route::get('getRooms', 'getRooms')->name('document-header-details.getRooms');
            Route::get('get-booking-Reports', 'getBookingReport');

            Route::get('logs/{id}', 'logs')->name('document-header-details.logs');
            Route::get('/', 'all')->name('document-header-details.index');
            Route::get('report-document', 'reportDocument');
            Route::get('annual-financial-report', 'annualFinancialReport');
            Route::get('annual-financial-report-detail', 'annualFinancialReportDetail');
            Route::get('panel-usage-rate-report', 'panelUsageRateReport');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('document-header-details.create');
            Route::put('/{id}', 'update')->name('document-header-details.update');
            Route::delete('/{id}', 'delete')->name('document-header-details.destroy');
            Route::post("bulk-delete", "bulkDelete");

        });
    });

});

Route::group(['prefix' => 'sectors'], function () {
    Route::controller(\App\Http\Controllers\Sector\SectorController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown')->name('sectors.getDropDown');
        Route::get('logs/{id}', 'logs')->name('sectors.logs');
        Route::get('/', 'all')->name('sectors.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('sectors.create');
        Route::put('/{id}', 'update')->name('sectors.update');
        Route::delete('/{id}', 'delete')->name('sectors.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'customers-categories'], function () {
    Route::controller(\App\Http\Controllers\CustomerCategory\CustomerCategoryController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown')->name('customers-categories.getDropDown');
        Route::get('/', 'all')->name('customers-categories.index');
        Route::get('logs/{id}', 'logs')->name('customers-categories.logs');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('customers-categories.create');
        Route::put('/{id}', 'update')->name('customers-categories.update');
        Route::delete('/{id}', 'delete')->name('customers-categories.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'customer-sources'], function () {
    Route::controller(\App\Http\Controllers\CustomerSource\CustomerSourceController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown')->name('customer-sources.getDropDown');
        Route::get('logs/{id}', 'logs')->name('customer-sources.logs');
        Route::get('/', 'all')->name('customer-sources.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('customer-sources.create');
        Route::put('/{id}', 'update')->name('customer-sources.update');
        Route::delete('/{id}', 'delete')->name('customer-sources.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'voucher-headers'], function () {
    Route::controller(\App\Http\Controllers\VoucherHeader\VoucherHeaderController::class)->group(function () {
        Route::get('logs/{id}', 'logs')->name('voucher-headers.logs');
        Route::get('/', 'all')->name('voucher-headers.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('voucher-headers.create');
        Route::put('/{id}', 'update')->name('voucher-headers.update');
        Route::delete('/{id}', 'delete')->name('voucher-headers.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'customer-groups'], function () {
    Route::controller(\App\Http\Controllers\CustomerGroup\CustomerGroupController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown')->name('customer-groups.getDropDown');
        Route::get('logs/{id}', 'logs')->name('customer-groups.logs');
        Route::get('/', 'all')->name('customer-groups.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('customer-groups.create');
        Route::put('/{id}', 'update')->name('customer-groups.update');
        Route::delete('/{id}', 'delete')->name('customer-groups.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'brands'], function () {
    Route::controller(\App\Http\Controllers\Brand\BrandController::class)->group(function () {
        Route::get('logs/{id}', 'logs')->name('brands.logs');
        Route::get('/', 'all')->name('brands.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('brands.create');
        Route::put('/{id}', 'update')->name('brands.update');
        Route::delete('/{id}', 'delete')->name('brands.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'groups'], function () {
    Route::controller(\App\Http\Controllers\Group\GroupController::class)->group(function () {
        Route::get('logs/{id}', 'logs')->name('groups.logs');
        Route::get('/', 'all')->name('groups.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('groups.create');
        Route::put('/{id}', 'update')->name('groups.update');
        Route::delete('/{id}', 'delete')->name('groups.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'attributes'], function () {
    Route::controller(\App\Http\Controllers\Attribute\AttributeController::class)->group(function () {
        Route::get('logs/{id}', 'logs')->name('attributes.logs');
        Route::get('/', 'all')->name('attributes.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('attributes.create');
        Route::put('/{id}', 'update')->name('attributes.update');
        Route::delete('/{id}', 'delete')->name('attributes.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'taxes'], function () {
    Route::controller(\App\Http\Controllers\Tax\TaxController::class)->group(function () {
        Route::get('logs/{id}', 'logs')->name('taxes.logs');
        Route::get('/', 'all')->name('taxes.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('taxes.create');
        Route::put('/{id}', 'update')->name('taxes.update');
        Route::delete('/{id}', 'delete')->name('taxes.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'report-documents'], function () {
    Route::controller(\App\Http\Controllers\Document\ReportDocumentController::class)->group(function () {
        Route::get('customer-statement-of-account', 'getCustomerStatementOfAccount');
    });
});

Route::group(['prefix' => 'General-item'], function () {
    Route::controller(\App\Http\Controllers\GeneralItemController::class)->group(function () {
        Route::get('/', 'all')->name('General-item.index');
        Route::get('logs/{id}', 'logs')->name('General-item.logs');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('General-item.create');
        Route::put('/{id}', 'update')->name('General-item.update');
        Route::delete('/{id}', 'delete')->name('General-item.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

//---------------------milad routes---------------------

Route::get('serial/branch', [SerialController::class, 'getSerialByBranchId']);

Route::resource('branches', BranchController::class)->except('create', 'edit');
Route::resource('serials', SerialController::class)->except('create', 'edit');
Route::get('cities/get-drop-down', [CityController::class, 'getDropDown']);

Route::resource('cities', CityController::class)->except('create', 'edit');
Route::get('cities/logs/{id}', [CityController::class, 'logs']);
Route::post('cities/bulk-delete', [CityController::class, 'bulkDelete']);
Route::resource('currencies', CurrencyController::class)->except('create', 'edit');
Route::get('currencies/logs/{id}', [CurrencyController::class, 'logs']);
Route::post('currencies/bulk-delete', [CurrencyController::class, 'bulkDelete']);
Route::resource('role_types', RoleTypeController::class)->except('create', 'edit');
Route::group(['prefix' => 'tree-properties'], function () {
    Route::controller(TreePropertyController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown');
        Route::get('root-nodes', 'getRootNodes');
        Route::get('/child-nodes/{parentId}', 'getChildNodes');
        Route::post('bulk-create', 'bulkCreate');
    });
});

Route::resource('tree-properties', TreePropertyController::class)->except('create', 'edit');
Route::get('tree-properties/logs/{id}', [TreePropertyController::class, 'logs']);
Route::post('tree-properties/bulk-delete', [TreePropertyController::class, 'bulkDelete']);

Route::resource('screen-tree-properties', ScreenTreePropertyController::class)->except('create', 'edit');
Route::get('screen-tree-properties/logs/{id}', [ScreenTreePropertyController::class, 'logs']);
Route::post('screen-tree-properties/bulk-delete', [ScreenTreePropertyController::class, 'bulkDelete']);

Route::resource('internal-salesman', InternalSalesmanController::class)->except('create', 'edit');

Route::get('internal-salesman/logs/{id}', [InternalSalesmanController::class, 'logs']);
Route::post('internal-salesman/bulk-delete', [InternalSalesmanController::class, 'bulkDelete']);
Route::get('internal-salesman/logs/{id}', [InternalSalesmanController::class, 'logs']);

Route::get('general-customer/get-drop-down', [GeneralCustomerController::class, 'getDropDown']);
Route::resource('general-customer', GeneralCustomerController::class)->except('create', 'edit');
Route::get('general-customer/logs/{id}', [GeneralCustomerController::class, 'logs']);
Route::get('check-supplier', [GeneralCustomerController::class, 'getCheckSupplier']);
Route::post('general-customer/bulk-delete', [GeneralCustomerController::class, 'bulkDelete']);

Route::get('/suppliers/get-drop-down', [SupplierController::class, 'getDropDown']);
Route::resource('suppliers', SupplierController::class)->except('create', 'edit');
Route::get('suppliers/logs/{id}', [SupplierController::class, 'logs']);
Route::post('suppliers/bulk-delete', [SupplierController::class, 'bulkDelete']);

Route::post('translation-update', [\App\Http\Controllers\TranslationController::class, 'update']);
Route::post('translation-delete', [\App\Http\Controllers\TranslationController::class, 'delete']);
//------------------------------------------------------

Route::post('/ocr/upload', [\App\Http\Controllers\OCRController::class, 'upload']); //https://packagist.org/packages/thiagoalessio/tesseract_ocr //sudo port install tesseract-<langcode>

Route::post("general_upload", function () {
    request()->file("file")->store("", "public_uploads");
});

// backups
Route::group(['prefix' => 'backups'], function () {
    Route::controller(\App\Http\Controllers\DatabaseBackupController::class)->group(function () {
        Route::get('/', 'all')->name('backups.index');
        Route::post('/', 'create')->name('backups.create');
    });
});

Route::group(['prefix' => 'client-types'], function () {
    Route::controller(\App\Http\Controllers\ClientType\ClientTypeController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown');

        Route::get('/', 'all')->name('client-types.index');
        Route::get('logs/{id}', 'logs')->name('client-types.logs');
    });
});

Route::group(['prefix' => 'document-module-type'], function () {
    Route::controller(\App\Http\Controllers\DocumentModuleType\DocumentModuleTypeController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown');
        Route::get('/get-drop-down-model', 'getDropDawnModel');
        Route::get('/', 'all')->name('document-module-type.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('document-module-type.create');
        Route::put('/{id}', 'update')->name('document-module-type.update');
        Route::delete('/{id}', 'delete')->name('document-module-type.destroy');
        Route::get('logs/{id}', 'logs')->name('document-module-type.logs');
        Route::post("bulk-delete", "bulkDelete");

    });
});

Route::group(['prefix' => 'break-settlements'], function () {
    Route::controller(\App\Http\Controllers\BreakSettlement\BreakSettlementController::class)->group(function () {
        Route::get('/', 'all')->name('break-settlements.index');
        Route::get('logs/{id}', 'logs')->name('break-settlements.logs');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('break-settlements.create');
        Route::put('/{id}', 'update')->name('break-settlements.update');
        Route::delete('/{id}', 'delete')->name('break-settlements.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'document-company-module-status'], function () {
    Route::controller(\App\Http\Controllers\DocumentCompanyModuleStatus\DocumentCompanyModuleStatusController::class)->group(function () {
        Route::get('/', 'all')->name('document-company-module-status.index');
        Route::get('logs/{id}', 'logs')->name('document-company-module-status.logs');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('document-company-module-status.create');
        Route::put('/{id}', 'update')->name('document-company-module-status.update');
        Route::delete('/{id}', 'delete')->name('document-company-module-status.destroy');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'general-accounts'], function () {
    Route::controller(\App\Http\Controllers\AccountController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown');
        Route::get('/', 'all')->name('general-accounts.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('general-accounts.create');
        Route::put('/{id}', 'update')->name('general-accounts.update');
        Route::delete('/{id}', 'delete')->name('general-accounts.destroy');
        Route::get('logs/{id}', 'logs')->name('general-accounts.logs');
        Route::post("bulk-delete", "bulkDelete");
    });
});

Route::group(['prefix' => 'general-main-cost-centers'], function () {
    Route::controller(\App\Http\Controllers\GeneralMainCostCentersController::class)->group(function () {
        Route::get('/get-drop-down', 'getDropDown');
        Route::get('/', 'all')->name('general-main-cost-centers.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('general-main-cost-centers.create');
        Route::put('/{id}', 'update')->name('general-main-cost-centers.update');
        Route::delete('/{id}', 'delete')->name('general-main-cost-centers.destroy');
        Route::get('logs/{id}', 'logs')->name('general-main-cost-centers.logs');
        Route::post("bulk-delete", "bulkDelete");
    });
});
