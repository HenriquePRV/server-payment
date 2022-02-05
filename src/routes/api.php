<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users',[UserController::class, 'users'])->name('list-users');
Route::get('/users/create',[UserController::class, 'create'])->name('create-users');
Route::get('/filter-user/{user}', [UserController::class, 'filterUser'])->name('filter-user');
Route::get('/transactions',[TransactionController::class, 'transactions'])->name('list-Transacations');
Route::get('/filter-transaction/{transaction}', [TransactionController::class, 'filterTransaction'])->name('filter-transaction');

Route::post('/fire-transaction', [TransactionController::class, 'create'])->name('create-transaction');
Route::post('/plus-money',[UserController::class, 'plusMoney'])->name('plus');
