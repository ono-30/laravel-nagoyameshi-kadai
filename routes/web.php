<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);

    /*個別で指定したい場合
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
*/
});

/*
// UserController
Route::controller(UserController::class)->group(function () {
    Route::get('admin/users/index', 'index')->name('admin.users.index');
    Route::get('admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
});
*/