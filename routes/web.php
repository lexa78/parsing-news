<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

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

Route::get('/', [NewsController::class, 'index'])->name('mainPage');
Route::get('/news/{id}', [NewsController::class, 'showWholeNews'])->name('showWholeNews');
Route::get('/category/news/{id}', [NewsController::class, 'showAllNewsFromCategory'])->name('allNewsFromCategory');
