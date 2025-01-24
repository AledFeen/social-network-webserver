<?php

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

Route::get('/checkAuth', [\App\Http\Controllers\Auth\AuthController::class, 'checkAuth']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', [\App\Http\Controllers\Auth\AuthController::class, 'user']);
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::get('/privacy-settings', [\App\Http\Controllers\PrivacySettingsController::class, 'getSettings']);
    Route::put('/privacy-settings', [\App\Http\Controllers\PrivacySettingsController::class, 'updateSettings']);

    Route::get('/my-account', [\App\Http\Controllers\AccountController::class, 'getMyAccount']);
    Route::put('/my-account', [\App\Http\Controllers\AccountController::class, 'updateAccount']);
    Route::get('/my-avatar', [\App\Http\Controllers\AccountController::class, 'getMyAvatar']);
    Route::get('/profile-image/{filename}', [\App\Http\Controllers\FileController::class, 'getAccountImage']);
    Route::post('/profile-image', [\App\Http\Controllers\AccountController::class, 'updateImage']);
    Route::delete('/profile-image', [\App\Http\Controllers\AccountController::class, 'deleteImage']);

    Route::get('/profile', [\App\Http\Controllers\AccountController::class, 'getProfile']);
    Route::get('/search-profile', [\App\Http\Controllers\AccountController::class, 'getSearchProfiles']);

    Route::get('/check-relations', [\App\Http\Controllers\SubscriptionController::class, 'checkRelations']);

    Route::group(['middleware' => ['account_type']], function () {
        Route::get('/subscribers', [\App\Http\Controllers\SubscriptionController::class, 'getSubscribers']);
        Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'getSubscriptions']);
        Route::get('/posts', [\App\Http\Controllers\PostController::class, 'getPosts']);
    });

    Route::group(['middleware' => ['account_type_post']], function () {
        Route::get('/post', [\App\Http\Controllers\PostController::class, 'getPost']);
        Route::get('/comments', [\App\Http\Controllers\PostController::class, 'getComments']);
        Route::get('/likes', [\App\Http\Controllers\PostController::class, 'getPostLikes']);
        Route::get('/reposts', [\App\Http\Controllers\PostController::class, 'getReposts']);
    });
    Route::get('/comment-replies',  [\App\Http\Controllers\PostController::class, 'getCommentReplies']);

    Route::get('/post-image/{filename}', [\App\Http\Controllers\FileController::class, 'getPostImage']);
    Route::get('/feed-posts', [\App\Http\Controllers\PostController::class, 'getFeedPosts']);

    //need checking account types
    Route::get('/posts-by-tag', [\App\Http\Controllers\PostController::class, 'getPostsByTag']);
    Route::get('/recommended-posts', [\App\Http\Controllers\PostController::class, 'getRecommendedPosts']);

    Route::delete('ignore', [\App\Http\Controllers\PreferredTagController::class, 'ignore']);

    Route::get('/blocked-users', [\App\Http\Controllers\BlacklistController::class, 'getBlockedUsers']);
    Route::post('/block-user', [\App\Http\Controllers\BlacklistController::class, 'block']);
    Route::delete('/unblock-user', [\App\Http\Controllers\BlacklistController::class, 'unblock']);

    Route::post('/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribeUser']);
    Route::delete('/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribeUser']);
    Route::delete('/delete-subscriber', [\App\Http\Controllers\SubscriptionController::class, 'deleteSubscriber']);

    Route::post('subscribe-request', [\App\Http\Controllers\SubscriptionRequestController::class, 'subscribe']);
    Route::post('accept-request', [\App\Http\Controllers\SubscriptionRequestController::class, 'acceptRequest']);
    Route::post('decline-request', [\App\Http\Controllers\SubscriptionRequestController::class, 'declineRequest']);
    Route::get('subscription-requests', [\App\Http\Controllers\SubscriptionRequestController::class, 'getRequests']);

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

    Route::put('/comment', [\App\Http\Controllers\PostController::class, 'updateComment']);
    Route::delete('/comment', [\App\Http\Controllers\PostController::class, 'deleteComment']);
    Route::post('/like', [\App\Http\Controllers\PostController::class, 'likePost']);

    Route::get('/notification/followers', [\App\Http\Controllers\NotificationController::class, 'getFollowers']);
    Route::get('/notification/likes', [\App\Http\Controllers\NotificationController::class, 'getLikes']);
    Route::get('/notification/comments', [\App\Http\Controllers\NotificationController::class, 'getComments']);
    Route::get('/notification/comment-replies', [\App\Http\Controllers\NotificationController::class, 'getCommentReplies']);
    Route::get('/notification/reposts', [\App\Http\Controllers\NotificationController::class, 'getReposts']);

    Route::group(['middleware' => ['can_message']], function () {
        Route::post('/personal-chat', [\App\Http\Controllers\ChatController::class, 'createPersonalChat']);
    });

    Route::get('/chats', [\App\Http\Controllers\ChatController::class, 'getChats']);
    Route::get('/chat-users', [\App\Http\Controllers\ChatController::class, 'getChatUsers']);
    Route::delete('/chat', [\App\Http\Controllers\ChatController::class, 'deletePersonalChat']);
    Route::get('/messages', [\App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/message', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
    Route::put('/message', [\App\Http\Controllers\ChatController::class, 'updateMessageText']);
    Route::delete('/message', [\App\Http\Controllers\ChatController::class, 'deleteMessage']);

    Route::get('/complaint', [\App\Http\Controllers\ComplaintController::class, 'getComplaint']);
    Route::get('/complaints', [\App\Http\Controllers\ComplaintController::class, 'getComplaints']);
    Route::post('/complaint', [\App\Http\Controllers\ComplaintController::class, 'createComplaint']);
    Route::put('/complaint', [\App\Http\Controllers\ComplaintController::class, 'updateComplaint']);
});
