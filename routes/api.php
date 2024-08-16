<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/profile-image/{filename}', [\App\Http\Controllers\Files\ImageController::class, 'getAccountImage']);
    Route::post('/profile-image', [\App\Http\Controllers\AccountController::class, 'updateImage']);
    Route::delete('/profile-image', [\App\Http\Controllers\AccountController::class, 'deleteImage']);

    Route::get('/blocked-users', [\App\Http\Controllers\BlacklistController::class, 'getBlockedUsers']);
    Route::post('/block-user', [\App\Http\Controllers\BlacklistController::class, 'block']);
    Route::delete('/unblock-user', [\App\Http\Controllers\BlacklistController::class, 'unblock']);

    Route::get('/subscribers', [\App\Http\Controllers\SubscriptionController::class, 'getSubscribers']);
    Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'getSubscriptions']);
    Route::post('/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribeUser']);
    Route::delete('/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribeUser']);
    Route::delete('/delete-subscriber', [\App\Http\Controllers\SubscriptionController::class, 'deleteSubscriber']);

    Route::post('/post', [\App\Http\Controllers\PostController::class, 'createPost']);
    Route::delete('/post', [\App\Http\Controllers\PostController::class, 'deletePost']);
    Route::put('/post', [\App\Http\Controllers\PostController::class, 'updatePostText']);
    Route::post('/post-files', [\App\Http\Controllers\PostController::class, 'updatePostFiles']);
    Route::post('/comment', [\App\Http\Controllers\PostController::class, 'leaveComment']);
    Route::put('/comment', [\App\Http\Controllers\PostController::class, 'updateComment']);
    Route::post('/like', [\App\Http\Controllers\PostController::class, 'likePost']);
    Route::get('/likes', [\App\Http\Controllers\PostController::class, 'getPostLikes']);

    Route::group(['middleware' => ['account_type']], function () {
        Route::get('account-type-middleware', function () {
            return response()->json(['success' => true]);
        });
    });

    Route::group(['middleware' => ['can_comment']], function () {
        Route::get('can-comment-middleware', function () {
            return response()->json(['success' => true]);
        });
    });

    Route::group(['middleware' => ['can_repost']], function () {
        Route::get('can-repost-middleware', function () {
            return response()->json(['success' => true]);
        });
    });

    Route::group(['middleware' => ['can_message']], function () {
        Route::get('can-message-middleware', function () {
            return response()->json(['success' => true]);
        });
    });

});
