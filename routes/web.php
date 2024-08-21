<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;

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
/*Route::get('/', function () {
    return view('welcome');
});
*/

require __DIR__ . '/auth.php';

/*管理者としてログインしている状態でのみアクセスできるように認可を設定*/
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);
    Route::resource('restaurants', Admin\RestaurantController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('company', Admin\CompanyController::class)->only(['index', 'edit', 'update']);
    Route::resource('terms', Admin\TermController::class)->only(['index', 'edit', 'update']);

    /*個別で指定したい場合
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
*/
});

/*管理者としてログインしていない状態でのみアクセスできるように認可を設定*/
Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('restaurants', RestaurantController::class)->only(['index', 'show']);

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
        Route::get('/restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->name('restaurants.reviews.index');

        Route::group(['middleware' => [Subscribed::class]], function () {
            Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
            Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
            Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
            Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
            Route::get('/restaurants/{restaurant}/reviews/create', [ReviewController::class, 'create'])->name('restaurants.reviews.create');
            Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])->name('restaurants.reviews.store');
            Route::get('/restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('restaurants.reviews.edit');
            Route::match(['put', 'patch'], '/restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'update'])->name('restaurants.reviews.update');
            Route::delete('/restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'destroy'])->name('restaurants.reviews.destroy');
        });

        Route::group(['middleware' => [NotSubscribed::class]], function () {
            Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
            Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
        });
    });
});
