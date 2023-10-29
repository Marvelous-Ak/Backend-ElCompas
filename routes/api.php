<?php

use App\Http\Controllers\Api\CatalogCompasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(CatalogCompasController::class)->group(function (){
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