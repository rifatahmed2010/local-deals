<?php

use App\Http\Controllers\FavouriteDealController;
use App\Http\Controllers\AppInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('send_verification_code', [\App\Http\Controllers\AuthController::class, 'send_verification_code']);
Route::post('send_reset_code', [\App\Http\Controllers\AuthController::class, 'send_reset_code']);
Route::post('check_verification_code', [\App\Http\Controllers\AuthController::class, 'check_verification_code']);
Route::post('/signupapi', [\App\Http\Controllers\AuthController::class, 'signupapi']);
Route::post('loginapi',[\App\Http\Controllers\AuthController::class,'loginapi']);
Route::post('school_request',[\App\Http\Controllers\AuthController::class,'schoolRequest']);


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('feature_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'featureDealListA']);
Route::post('daily_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'dailyDealListA']);
Route::post('onetime_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'oneTimeDealListA']);
Route::post('monthly_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'monthlyDealListA']);
Route::post('weekly_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'weeklyDealListA']);
Route::post('limited_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'limitedDealListA']);
Route::post('anytime_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'anytimeDealListA']);
Route::post('search_deal_list_anonymous',[\App\Http\Controllers\DealController::class,'searchDealListA']);
Route::post('reset_password',[\App\Http\Controllers\AuthController::class,'resetPassword']);
Route::post('university-list',[\App\Http\Controllers\AuthController::class,'universities']);

Route::post('deals-by-tag-name',[\App\Http\Controllers\DealController::class,'dealsByTagName']);
Route::post('search_deals_by_tag',[\App\Http\Controllers\DealController::class,'searchDealsByTag']);
Route::post('home_deals',[\App\Http\Controllers\DealController::class,'homeDeals']);
Route::post('deal_clicka',[\App\Http\Controllers\DealController::class,'dealClick']);
Route::post('notification_click',[\App\Http\Controllers\DealController::class,'notificationClick']);
Route::post('deal-claim-click-data',[\App\Http\Controllers\ChartController::class,'getChartData']);
Route::post('time-performance-anlysis-data',[\App\Http\Controllers\ChartController::class,'gettimeBasedPerformance']);
Route::post('update-deal-order',[\App\Http\Controllers\DealController::class,'updateDealOrder']);
Route::get('push-notification',[\App\Http\Controllers\PushNotificationController::class,'sendScheduledNotification']);
Route::post('get-app-info',[AppInfoController::class,'getInfo']);

Route::middleware('auth:sanctum')->group(function (){
    Route::delete('logoutapi',[\App\Http\Controllers\AuthController::class,'logoutapi']);
    Route::post('feature_deal_list',[\App\Http\Controllers\DealController::class,'featureDealList']);
    Route::post('daily_deal_list',[\App\Http\Controllers\DealController::class,'dailyDealList']);
    Route::post('onetime_deal_list',[\App\Http\Controllers\DealController::class,'oneTimeDealList']);
    Route::post('monthly_deal_list',[\App\Http\Controllers\DealController::class,'monthlyDealList']);
    Route::post('weekly_deal_list',[\App\Http\Controllers\DealController::class,'weeklyDealList']);
    Route::post('limited_deal_list',[\App\Http\Controllers\DealController::class,'limitedDealList']);
    Route::post('anytime_deal_list',[\App\Http\Controllers\DealController::class,'anytimeDealList']);
    Route::post('claim_deal',[\App\Http\Controllers\DealController::class,'claim_deal']);
    Route::post('get_deal_by_id',[\App\Http\Controllers\DealController::class,'getDealById']);
    Route::post('profile',[\App\Http\Controllers\AuthController::class,'profile']);
    Route::post('profile_update',[\App\Http\Controllers\AuthController::class,'profileUpdate']);
    Route::post('push_notification_token_update',[\App\Http\Controllers\AuthController::class,'pushTokenUpdate']);
    Route::post('add_contact_information',[\App\Http\Controllers\ContactInformationController::class,'addContactInformation']);
    Route::post('add_business_request',[\App\Http\Controllers\BusinessRequestController::class,'addBusinessRequest']);
    Route::post('leaderboard',[\App\Http\Controllers\AuthController::class,'leaderboard']);
    Route::delete('permanently_delete_profile',[\App\Http\Controllers\AuthController::class,'permanentlyDeleteProfile']);
    Route::post('search_deal',[\App\Http\Controllers\DealController::class,'searchDealList']);
    Route::post('your-deal-history',[\App\Http\Controllers\DealController::class,'yourDealHistory']);
    Route::post('deal_click',[\App\Http\Controllers\DealController::class,'dealClick']);

    Route::post('add-favourite',[FavouriteDealController::class,'addFavourite']);
    Route::post('remove-favourite',[FavouriteDealController::class,'removeFavourite']);
    Route::post('favourite-deal-list',[FavouriteDealController::class,'favouriteListByUser']);
    Route::post('insert-app-info',[AppInfoController::class,'insertInfo']);
});
