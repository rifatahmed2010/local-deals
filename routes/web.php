<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\BusinessRequestController;
use Illuminate\Support\Facades\Route;

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
    return view('auth.login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/policy', [DashboardController::class, 'policy'])->name('policy');
Route::get('/terms', [DashboardController::class, 'terms'])->name('terms');
Route::get('/chart', [ChartController::class, 'index']);
Route::get('/timebased-performance', [ChartController::class, 'timePerformance']);
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/signup',[AuthController::class,'showRegistration'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);
    /*Users*/
    Route::get('/users',[UserController::class, 'index'])->name('users');
    Route::get('/user/show/{id}', [UserController::class, 'show'])->middleware('admin')->name('user.show');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->middleware('admin')->name('user.edit');
    Route::post('/user/update', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    /*..Users..*/

    /*Dealas Start*/

    Route::resource('deals', DealController::class);
    Route::get('/deals/destroy/{id}', [DealController::class, 'destroy'])->name('deals.destroy');
    Route::post('/deals/reorder', [DealController::class, 'reorder'])->name('deals.reorder');
    /*Deals End*/

    /*Contact Information*/
    Route::resource('business_requests', \App\Http\Controllers\BusinessRequestController::class);
    Route::get('/school_requests', [\App\Http\Controllers\AuthController::class,'school_requests']);
    Route::get('/business_requests/destroy/{id}', [BusinessRequestController::class, 'destroy'])->name('business_requests.destroy');
    Route::resource('contact_informations', \App\Http\Controllers\ContactInformationController::class);
    Route::get('/contact_informations/destroy/{id}', [\App\Models\ContactInformation::class, 'destroy'])->name('contact_informations.destroy');
    /*Contact Information End*/

    /*Business Request*/

    /*Business Request End*/

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::get('analytic-deal', [\App\Http\Controllers\AnalyticController::class, 'deal'])->name('analytic-deal');
    Route::get('analytic-user', [\App\Http\Controllers\AnalyticController::class, 'user'])->name('analytic-user');
    Route::get('business-analytic', [\App\Http\Controllers\AnalyticController::class, 'business'])->name('business-analytic');

    Route::resource('push_notifications', \App\Http\Controllers\PushNotificationController::class);
//Route::post('/push_notifications/sendNotification', [PushNotificationController::class, 'sendNotification'])->name('push_notifications.send');
    Route::post('/send_notifications', [PushNotificationController::class, 'sendNotifications'])->name('send_notifications');

    Route::get('/chart-data', [ChartController::class, 'getChartData']);
    Route::get('/timebased-performance', [ChartController::class, 'timeBasedPerformance'])->name('timebased-performance');

    Route::resource('universities', UniversityController::class);

});
