<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/gl', function (Request $request) {
    return $request->user();
});

Route::prefix ('gl')->group (function (){
    Route ::resource ( 'coa' , 'GlCoaController' ) -> except ( 'edit' , 'create' );
    Route ::get ( 'coa/logs/{id}' , 'GlCoaController@logs' );
    Route ::post ( 'coa/bulk-delete' , 'GlCoaController@bulkDelete' );

    Route ::resource ( 'account-type' , 'GlAccountTypeController' ) -> except ( 'edit' , 'create' );
    Route ::get ( 'account-type/logs/{id}' , 'GlAccountTypeController@logs' );
    Route ::post ( 'account-type/bulk-delete' , 'GlAccountTypeController@bulkDelete' );
});
