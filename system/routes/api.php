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
Route::middleware( 'auth:api' )->group( function() {
    #user routes
    Route::prefix( 'users' )->group( function() {
        Route::get( 'details', function( Request $request ) {
            return $request->user();
        });
        Route::get( 'lists', 'API\UsersController@list' );
        Route::post( 'add', 'API\UsersController@add' );
        Route::post( 'update', 'API\UsersController@update' );
        Route::post( 'delete', 'API\UsersController@delete' );
        Route::post( 'change-password', 'API\UsersController@change_password' );
        Route::post( 'logout', 'API\UsersController@logout' );
    });

    Route::prefix( 'expenses' )->group( function() {
        Route::get( 'lists', 'API\RolesController@list' );
        Route::post( 'add', 'API\RolesController@add' );
        Route::post( 'update', 'API\RolesController@update' );
        Route::post( 'delete', 'API\RolesController@delete' );
    });

    Route::prefix( 'roles' )->group( function() {
        Route::prefix( 'categories' )->group( function() {
            Route::get( 'lists', 'API\ExpenseCategoriesController@list' );
            Route::post( 'add', 'API\ExpenseCategoriesController@add' );
            Route::post( 'update', 'API\ExpenseCategoriesController@update' );
            Route::post( 'delete', 'API\ExpenseCategoriesController@delete' );
        });

        Route::get( 'lists', 'API\ExpensesController@list' );
        Route::post( 'add', 'API\ExpenseCategoriesController@add' );
        Route::post( 'update', 'API\ExpensesController@update' );
        Route::post( 'delete', 'API\ExpensesController@delete' );
        Route::get( 'total', 'API\ExpensesController@total' );
    });
});

Route::prefix( 'users' )->group( function() {
    Route::post( 'login', 'API\UsersController@login' );
});
