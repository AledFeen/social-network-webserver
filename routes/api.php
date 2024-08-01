<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/privacy-settings', [\App\Http\Controllers\PrivacySettingsController::class, 'getSettings']);
    Route::put('/privacy-settings', [\App\Http\Controllers\PrivacySettingsController::class, 'updateSettings']);

    Route::get('/account', [\App\Http\Controllers\AccountController::class, 'getAccount']);
    Route::get('/my-account', [\App\Http\Controllers\AccountController::class, 'getMyAccount']);
    Route::put('/my-account', [\App\Http\Controllers\AccountController::class, 'updateAccount']);
    Route::post('/profile-image', [\App\Http\Controllers\AccountController::class, 'updateImage']);
    Route::delete('/profile-image', [\App\Http\Controllers\AccountController::class, 'deleteImage']);

    Route::get('/subscribers', [\App\Http\Controllers\SubscriptionController::class, 'getSubscribers']);
    Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'getSubscriptions']);
    Route::post('/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribeUser']);
    Route::delete('/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribeUser']);
    Route::delete('/delete-subscriber', [\App\Http\Controllers\SubscriptionController::class, 'deleteSubscriber']);
});
