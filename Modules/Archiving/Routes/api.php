<?php

use Illuminate\Support\Facades\Route;
use Modules\Archiving\Http\Controllers\ArchiveFileController;
use Modules\Archiving\Http\Controllers\ClosedReferenceController;
use Modules\Archiving\Http\Controllers\DepartmentController;
use Modules\Archiving\Http\Controllers\DocStatusController;
use Modules\Archiving\Http\Controllers\DocTypeController;
use Modules\Archiving\Http\Controllers\DocTypeDepartmentController;
use Modules\Archiving\Http\Controllers\DocTypeFieldController;
use Modules\Archiving\Http\Controllers\DocumentController;
use Modules\Archiving\Http\Controllers\DocumentDtlController;
use Modules\Archiving\Http\Controllers\DocumentFieldController;

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

// Route::middleware('auth:api')->get('/archiving', function (Request $request) {
//     return $request->user();
// });



Route::group(['prefix' => 'document-field'], function () {
    Route::controller(DocumentFieldController::class)->group(function () {
        Route::get("tables", 'getTables');
        Route::get("columns/{table}", 'getColumns');
        Route::get("column-data/{table}/{column}", 'getColumnData');
        Route::get('/', 'all')->name('archive.document.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('archive.document.create');
        Route::put('/{id}', 'update')->name('archive.document.update');
        Route::delete('/{id}', 'delete')->name('archive.document.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'archive-closed-reference'], function () {
    Route::controller(ClosedReferenceController::class)->group(function () {
        Route::get('/', 'all')->name('archiveClosedReference.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('archiveClosedReference.create');
        Route::put('/{id}', 'update')->name('archiveClosedReference.update');
        Route::delete('/{id}', 'delete')->name('archiveClosedReference.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'arch-department'], function () {
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('/', 'all')->name('archDepartment.index');
        Route::get('parent_department', 'parentDepartment')->name('parent_department.index');
        Route::get('/tree', 'tree');
        Route::get('/only-has-key', 'onlyHasKey');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('archDepartment.create');
        Route::put('/{id}', 'update')->name('archDepartment.update');
        Route::delete('/{id}', 'delete')->name('archDepartment.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'arch-document'], function () {
    Route::controller(DocumentController::class)->group(function () {
        Route::get('/', 'all')->name('archDocument.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('archDocument.create');
        Route::put('/{id}', 'update')->name('archDocument.update');
        Route::delete('/{id}', 'delete')->name('archDocument.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'arch-doc-type'], function () {
    Route::controller(DocTypeController::class)->group(function () {
        Route::get("/tree", 'tree')->name('arch-doc-type.tree');
        Route::get("/nodes-level-two", 'nodesLevelTwo');
        Route::get("/docTypeChildArchiveFiles", 'docTypeChildArchiveFiles');
        Route::get('/', 'all')->name('arch-doc-type.index');
        Route::post('add-status-to-document', 'addStatusToDocumentType');
        Route::delete('remove-status-from-document/{doc_type_id}/{status_id}', 'removeStatusFromDocumentType');
        Route::get('/doc-statuses/{id}', 'getDocTypeStatuses');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('arch-doc-type.create');
        Route::put('/{id}', 'update')->name('arch-doc-type.update');
        Route::delete('/{id}', 'delete')->name('arch-doc-type.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'arch-doc-type-field'], function () {
    Route::controller(DocTypeFieldController::class)->group(function () {
        Route::get('/', 'all')->name('arch-doc-type-field.index');
        Route::get('/{id}', 'find');
        Route::get('id-doctype-field/{id}', 'idDocTypeField');
        Route::post('/', 'create')->name('arch-doc-type-field.create');
        Route::put('/{id}', 'update')->name('arch-doc-type-field.update');
        Route::delete('/{id}', 'delete')->name('arch-doc-type-field.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
        Route::post('bulk-add', 'bulkInsert');
        Route::post('bulk-update', 'bulkUpdate');
    });
});

Route::group(['prefix' => 'arch-document-dtl'], function () {
    Route::controller(DocumentDtlController::class)->group(function () {
        Route::get('/', 'all')->name('arch-document-dtl.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('arch-document-dtl.create');
        Route::put('/{id}', 'update')->name('arch-document-dtl.update');
        Route::delete('/{id}', 'delete')->name('arch-document-dtl.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::group(['prefix' => 'arch-doc-status'], function () {
    Route::controller(DocStatusController::class)->group(function () {
        Route::get('/', 'all')->name('archDocStatus.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('archDocStatus.create');
        Route::put('/{id}', 'update')->name('archDocStatus.update');
        Route::delete('/{id}', 'delete')->name('archDocStatus.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

//  department documents routes
Route::group(['prefix' => 'arch-doc-type-department'], function () {
    Route::controller(DocTypeDepartmentController::class)->group(function () {
        Route::get('/', 'all')->name('doc-type.department.index');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name('doc-type.department.create');
        Route::put('/{id}', 'update')->name('doc-type.department.update');
        Route::delete('/{id}', 'delete')->name('doc-type.department.destroy');
        Route::get('logs/{id}', 'logs');
        Route::get('getDepartmentByDocument/{id}', 'getDepartmentByDocument');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

//  archive files  routes
Route::group(['prefix' => 'arch-archive-files'], function () {
    Route::controller(ArchiveFileController::class)->group(function () {
        Route::get('valueMedia', 'valueMedia');
        Route::get('getKeys', 'getKeys');
        Route::get('files-Doc-Type/{id}', 'filesDocType');
        Route::get("/docType-child-archiv-files", 'docTypeChildArchiveFiles');
        Route::get('files_Department_Doc_Type', 'files_Department_Doc_Type');
        Route::get("/value/{value}", "searchValue");
        Route::get("pdf/{id}", "pdf");
        Route::put("toggle-favourite", "toggleFavourite");
        Route::get('/', 'all')->name(' archive.files.index');
        Route::get("/archive-files/{docTypeId}", 'getArchiveFiles');
        Route::get('/{id}', 'find');
        Route::post('/', 'create')->name(' archive.files.create');
        Route::put('/{id}', 'update')->name(' archive.files.update');
        Route::delete('/{id}', 'delete')->name(' archive.files.destroy');
        Route::get('logs/{id}', 'logs');
        Route::post('bulk-delete', 'bulkDelete');
    });
});

Route::resource('arch_document_status', 'DocumentStatusController')->except('edit', 'create');
Route::get('arch_document_status/logs/{id}', 'DocumentStatusController@logs');
Route::post('arch_document_status/bulk-delete', 'DocumentStatusController@bulkDelete');


//customTable
Route::group(['prefix' => 'ArchivingCustomTable'], function () {
    Route::controller(\Modules\Archiving\Http\Controllers\ArchivingCustomTableController::class)->group(function () {
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
