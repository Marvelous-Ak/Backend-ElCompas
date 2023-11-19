<?php

use App\Http\Controllers\Api\CatalogCompasController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(CatalogCompasController::class)->group(function (){ /// Rutas: CatalogCompasController
    Route::get('/catalogs/{id}','index');
    Route::post('/catalogos','create');
    Route::get('/catalogos','showAll');
    Route::get('/products/{id}', 'showOne');
    Route::put('/catalogs/{id}','update');
    Route::delete('/catalogs/{id}','deleteP');
    Route::get('/search/{name}', 'searchProduct');
    Route::get('/search2/{name}', 'searchName2');
    Route::get('/categories/{id}', 'showCate');
});

Route::controller(ProductController::class)->group(function (){ /// Rutas: ProductController
});

Route::controller(UserController::class)->group(function (){ /// Rutas: UserController
    Route::post('/user','create');
    Route::get('/user/{id}','read');
    Route::put('/user/{id}','update');
    Route::delete('/user/{id}','destroy');
    Route::post('/userAdmi','createAdmi');
    Route::post('/user/login','login');
});

Route::controller(CommentController::class)->group(function (){ /// Rutas: CommentController
});

Route::resource('comments_products', CommentProductController::class);

Route::controller(SupplierController::class)->group(function (){ /// Rutas: SupplierController
    Route::post('/supplier','create');
    Route::get('/supplier/{id}','read');
    Route::put('/supplier/{id}','update');
    Route::delete('/supplier/{id}','delete');
    Route::get('/supplier','showAll');
});