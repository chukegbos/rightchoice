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
	
	Route::get('setting', 'API\UserController@setting');
	Route::put('setting/{id}', 'API\UserController@updateSetting');
	Route::get('stat', 'API\DashboardController@stat');
	
	Route::get('allusers', 'API\UserController@allusers');

	Route::group(['prefix' => 'user'], function(){
		Route::group(['prefix' => 'dashboard'], function(){
	    	Route::get('rep', 'API\DashboardController@rep');
	    	Route::get('sales', 'API\DashboardController@totalSales');
		});

		Route::get('bank', 'API\UserController@bank');
	    Route::post('fetchbank', 'API\UserController@fetchbank');
	    Route::get('/', 'API\UserController@getUser');
	    Route::get('{id}', 'API\UserController@viewUser');
	});

	Route::group(['prefix' => 'cart'], function(){
    	Route::get('testqty', 'API\CartController@testqty');
    	Route::get('getorder/{id}', 'API\DashboardController@getorder');
    	Route::post('setqty', 'API\CartController@setqty');
    	Route::get('addCart', 'API\CartController@addToCart');
    	Route::get('addCart/{id}', 'API\CartController@addCart');
    	Route::get('getCart', 'API\CartController@getCart');
    	Route::get('addOne/{id}', 'API\CartController@addOne');
    	Route::get('reduceOne/{id}', 'API\CartController@reduceOne');
    	Route::get('reduceAll/{id}', 'API\CartController@reduceAll');
    	Route::post('checkout', 'API\CartController@checkout');
	});

	Route::group(['prefix' => 'purchases'], function(){
    	Route::get('/', 'API\PurchaseController@index');
    	Route::post('/', 'API\PurchaseController@store');
    	Route::put('{id}', 'API\PurchaseController@update');
    	Route::get('{id}', 'API\PurchaseController@show');
    	Route::delete('{id}', 'API\PurchaseController@destroy');
	});

    Route::group(['prefix' => 'store'], function(){
    	Route::get('makerequest', 'API\StoreRequestController@makerequest');
    	Route::get('moverequest', 'API\StoreRequestController@moverequest');
    	Route::get('moveback', 'API\StoreRequestController@moveback');
    	Route::get('getrequest/{req_id}', 'API\StoreRequestController@getrequest');
    	Route::get('getmove/{ref_id}', 'API\StoreRequestController@getMove');
    	Route::get('viewrequest/{req_id}', 'API\StoreRequestController@viewrequest');
    	Route::get('request', 'API\StoreRequestController@index');
    	Route::get('delete', 'API\StoreController@destroy');
    	Route::post('request', 'API\StoreRequestController@store');
    	Route::post('move', 'API\StoreRequestController@storemove');
    	Route::delete('request/{id}', 'API\StoreRequestController@destroy');
    	Route::put('request/{id}', 'API\StoreRequestController@edit');
    	Route::get('/accept/{id}', 'API\StoreController@accept');
    	Route::get('/inventory', 'API\StoreController@getInventory');
    	Route::get('/tradeinventory', 'API\StoreController@tradeinventory');
    	Route::get('/transferinventory/{id}', 'API\StoreController@transferinventory');
    	Route::get('/roominventory', 'API\StoreController@roominventory');
    	Route::get('/target/{code}', 'API\StoreController@target');
    	Route::post('/target', 'API\StoreController@storetarget');
    	Route::put('/target', 'API\StoreController@updatetarget');
    	Route::get('/allinventory', 'API\StoreController@allInventory');
    	Route::get('/reports', 'API\StoreController@reports');
    	Route::get('/debtors', 'API\StoreController@debtors');
    	Route::post('/debt', 'API\StoreController@addDebt');
    	Route::get('/debtors/{sale_id}', 'API\StoreController@debtorview');
	});

	Route::group(['prefix' => 'cashier'], function(){
	    Route::get('', 'API\SalesRepController@index');
	    Route::post('', 'API\SalesRepController@store');
	    Route::put('{id}', 'API\SalesRepController@update');
	    Route::delete('{id}', 'API\SalesRepController@destroy');
    });

    Route::group(['prefix' => 'customer'], function(){
	    Route::get('', 'API\CustomerController@index');
	    Route::post('', 'API\CustomerController@store');
	    Route::put('{id}', 'API\CustomerController@update');
	    Route::delete('{id}', 'API\CustomerController@destroy');
    });

    Route::group(['prefix' => 'admin'], function(){
	    Route::get('', 'API\UserController@index');
	    Route::post('', 'API\UserController@store');
	    Route::put('{id}', 'API\UserController@update');
	    Route::delete('{id}', 'API\UserController@destroy');
	    Route::get('inventory', 'API\InventoryController@getInventory');
	    Route::get('sales', 'API\DashboardController@orders');
    	Route::get('/order-report', 'API\DashboardController@orderReport');
    	Route::get('/discharge', 'API\StoreController@discharge');
    	Route::put('/discharge/{id}', 'API\StoreController@approve');
    	//Route::get('/discharge/{id}', 'API\StoreController@approve');
    	Route::post('discharge', 'API\StoreController@approve');
    	Route::get('/decline/{id}', 'API\StoreController@decline');
    });

    //Bar manager route
    Route::group(['prefix' => 'manager'], function(){
	    Route::get('', 'API\ManagerController@index');
	    Route::post('', 'API\ManagerController@store');
	    Route::put('{id}', 'API\ManagerController@update');
	    Route::delete('{id}', 'API\ManagerController@destroy');
    });

    Route::group(['prefix' => 'suppliers'], function(){
    	Route::get('/', 'API\SupplierController@index');
    	Route::get('all', 'API\SupplierController@allSuppliers');
    	Route::get('/{id}', 'API\SupplierController@singleSupplier');
    	Route::post('/', 'API\SupplierController@store');
    	Route::put('{id}', 'API\SupplierController@update');
        Route::delete('{id}', 'API\SupplierController@destroy');
    });

    /** Inventory Route */
	Route::group(['prefix' => 'inventory'], function(){
		Route::group(['prefix' => 'category'], function(){
		    Route::get('', 'API\CategoryController@index');
		    Route::get('{id}', 'API\CategoryController@show');
		    Route::post('', 'API\CategoryController@store');
		    Route::put('{id}', 'API\CategoryController@update');
		    Route::delete('{id}', 'API\CategoryController@destroy');
	    });

	    Route::get('', 'API\InventoryController@index');
	    Route::post('/increase', 'API\InventoryController@increase');
	    Route::get('/purchase', 'API\InventoryController@purchase');
	    Route::get('/purchase/{product_id}', 'API\InventoryController@showpurchase');
	    Route::get('delete', 'API\InventoryController@destroy');
	    Route::get('{id}', 'API\InventoryController@show');
	    Route::post('', 'API\InventoryController@store');
	    Route::put('{id}', 'API\InventoryController@update');
	    
	    Route::delete('{id}', 'API\InventoryController@destroy');
    });
   
    Route::apiResources(['store' => 'API\StoreController']);

    /** Account Route */
	Route::group(['prefix' => 'account'], function(){
		Route::get('balance', 'API\IncomeController@balance');

		Route::group(['prefix' => 'income'], function(){
			Route::group(['prefix' => 'category'], function(){
			    Route::get('', 'API\IncomeCategoryController@index');
			    Route::get('{id}', 'API\IncomeCategoryController@show');
			    Route::post('', 'API\IncomeCategoryController@store');
			    Route::put('{id}', 'API\IncomeCategoryController@update');
			    Route::delete('{id}', 'API\IncomeCategoryController@destroy');
		    });

		    Route::get('', 'API\IncomeController@index');
		    Route::get('{id}', 'API\IncomeController@show');
		    Route::post('', 'API\IncomeController@store');
		    Route::put('{id}', 'API\CategoryController@update');
		    Route::delete('{id}', 'API\IncomeController@destroy');
	    });

	    Route::group(['prefix' => 'expenditure'], function(){
			Route::group(['prefix' => 'category'], function(){
			    Route::get('', 'API\ExpenditureCategoryController@index');
			    Route::get('{id}', 'API\ExpenditureCategoryController@show');
			    Route::post('', 'API\ExpenditureCategoryController@store');
			    Route::put('{id}', 'API\ExpenditureCategoryController@update');
			    Route::delete('{id}', 'API\ExpenditureCategoryController@destroy');
		    });

		    Route::get('', 'API\ExpenditureController@index');
		    Route::get('{id}', 'API\ExpenditureController@show');
		    Route::post('', 'API\ExpenditureController@store');
		    Route::put('{id}', 'API\ExpenditureController@update');
		    Route::delete('{id}', 'API\ExpenditureController@destroy');
	    });
    });