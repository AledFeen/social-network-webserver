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

    Route::get('/my-account', [\App\Http\Controllers\AccountController::class, 'getMyAccount']);
    Route::put('/my-account', [\App\Http\Controllers\AccountController::class, 'updateAccount']);
    Route::get('/profile-image/{filename}', [\App\Http\Controllers\Files\ImageController::class, 'getAccountImage']);
    Route::post('/profile-image', [\App\Http\Controllers\AccountController::class, 'updateImage']);
    Route::delete('/profile-image', [\App\Http\Controllers\AccountController::class, 'deleteImage']);

    //!!need to change
    Route::get('/account', [\App\Http\Controllers\AccountController::class, 'getAccount']);
    //
    Route::group(['middleware' => ['account_type']], function () {
        Route::get('/subscribers', [\App\Http\Controllers\SubscriptionController::class, 'getSubscribers']);
        Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'getSubscriptions']);
        Route::get('/posts', [\App\Http\Controllers\PostController::class, 'getPosts']);
    });

    Route::delete('ignore', [\App\Http\Controllers\PreferredTagController::class, 'ignore']);

    Route::get('/blocked-users', [\App\Http\Controllers\BlacklistController::class, 'getBlockedUsers']);
    Route::post('/block-user', [\App\Http\Controllers\BlacklistController::class, 'block']);
    Route::delete('/unblock-user', [\App\Http\Controllers\BlacklistController::class, 'unblock']);

    Route::post('/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribeUser']);
    Route::delete('/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribeUser']);
    Route::delete('/delete-subscriber', [\App\Http\Controllers\SubscriptionController::class, 'deleteSubscriber']);

    Route::get('/reposts', [\App\Http\Controllers\PostController::class, 'getReposts']);

    Route::group(['middleware' => ['can_repost']], function () {
        Route::post('/post', [\App\Http\Controllers\PostController::class, 'createPost']);
    });

    Route::delete('/post', [\App\Http\Controllers\PostController::class, 'deletePost']);
    Route::put('/post', [\App\Http\Controllers\PostController::class, 'updatePostText']);
    Route::post('/post-files', [\App\Http\Controllers\PostController::class, 'updatePostFiles']);
    Route::put('/post-tags', [\App\Http\Controllers\PostController::class, 'updatePostTags']);

    Route::group(['middleware' => ['can_comment']], function () {
        Route::post('/comment', [\App\Http\Controllers\PostController::class, 'leaveComment']);
    });

    Route::get('/comments', [\App\Http\Controllers\PostController::class, 'getComments']);
    Route::get('/comment-replies',  [\App\Http\Controllers\PostController::class, 'getCommentReplies']);
    Route::put('/comment', [\App\Http\Controllers\PostController::class, 'updateComment']);
    Route::delete('/comment', [\App\Http\Controllers\PostController::class, 'deleteComment']);
    Route::post('/like', [\App\Http\Controllers\PostController::class, 'likePost']);
    Route::get('/likes', [\App\Http\Controllers\PostController::class, 'getPostLikes']);

    Route::get('/notification/followers', [\App\Http\Controllers\NotificationController::class, 'getFollowers']);
    Route::get('/notification/likes', [\App\Http\Controllers\NotificationController::class, 'getLikes']);
    Route::get('/notification/comments', [\App\Http\Controllers\NotificationController::class, 'getComments']);
    Route::get('/notification/comment-replies', [\App\Http\Controllers\NotificationController::class, 'getCommentReplies']);
    Route::get('/notification/reposts', [\App\Http\Controllers\NotificationController::class, 'getReposts']);

    Route::group(['middleware' => ['can_message']], function () {
        Route::get('can-message-middleware', function () {
            return response()->json(['success' => true]);
        });
    });

});
