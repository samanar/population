<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

// routes for importing and converting populations
Route::get('population/import', 'PopulationController@import');
Route::get('population/convert', 'PopulationController@convert');

// routes for merging population database to iranCity tables

Route::get('merge', 'IranCityController@merge');
Route::get('favorites', 'IranCityController@findFavorites');
