<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SocialController;

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

// Google Login Routes
Route::group(['prefix' => 'google'], function() {
    Route::post('login' , [SocialController::class, 'google_login']);
});

// Facebook Login Routes
Route::group(['prefix' => 'facebook'], function() {
    Route::post('login' , [SocialController::class, 'facebookToken']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('Register', [ApiController::class, 'Register'])->name('register');
Route::post('login', [ApiController::class, 'login'])->name('login');
Route::get('interests', [ApiController::class, 'GetAllInterest'])->name('interests');
Route::post('forgot-password', [ApiController::class, 'forgot_password'])->name('forgotPassword');
Route::post('verify-otp', [ApiController::class, 'verify_otp'])->name('verifyOtp');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [ApiController::class, 'logout'])->name('logout');
    Route::get('profile', [ApiController::class, 'GetUserProfile'])->name('profile');
    Route::get('posts', [ApiController::class, 'GetPosts'])->name('posts');
    Route::post('posts/like', [ApiController::class, 'like_post']);
    Route::post('posts/dislike', [ApiController::class, 'dislike_post']);
    Route::post('posts/comment', [ApiController::class, 'post_comment']);
    Route::post('posts/comment/reply', [ApiController::class, 'post_comment_reply']);
    Route::post('get/comment-replies', [ApiController::class, 'get_comment_replies']);
    Route::post('get/comments', [ApiController::class, 'get_comments']);
    Route::delete('my-posts/{id}', [ApiController::class, 'delete_post']);
    Route::post('add-post', [ApiController::class, 'AddPost'])->name('Addpost');
    Route::post('edit-post', [ApiController::class, 'edit_post']);
    Route::post('update-profile', [ApiController::class, 'UpdateProfile'])->name('UpdateProfile');
    Route::post('post-question', [ApiController::class, 'Postquestion'])->name('post-question');

    Route::post('post-answer', [ApiController::class, 'Postanswers'])->name(' Post-answer');
    Route::get('my-questions', [ApiController::class, 'Getmyquestions'])->name('Getmyquestions');
    Route::delete('my-questions/{id}', [ApiController::class, 'delete_question']);
    Route::put('edit-question', [ApiController::class, 'edit_question']);
    Route::get('my-posts', [ApiController::class, 'GetMyPosts'])->name(' GetMyPosts');
    Route::get('all-questions', [ApiController::class, 'Getallquestions'])->name('Getallquestions');
    Route::get('my-interests', [ApiController::class, 'Getmyinterets'])->name('Getmyinterets');
    Route::post('post-my-interest', [ApiController::class, 'Postmyinterets'])->name('Postmyinterets');
    Route::post('update-interest', [ApiController::class, 'update_interest']);
    Route::delete('delete-user-image', [ApiController::class, 'delete_user_image']);

    Route::post('change-password', [ApiController::class, 'change_password'])->name('changePassword');
    Route::get('all-users', [ApiController::class, 'Getallusers'])->name('all-users');

    Route::group(['prefix' => 'search'], function() {
        Route::post('posts',[ApiController::class,'search_posts']);
        Route::post('questions',[ApiController::class,'search_questions']);
        Route::post('accounts',[ApiController::class,'search_accounts']);
    });
    // Notification Routes
    Route::post('send-notification', [ApiController::class, 'push_notification'])->name('send.notification');
    Route::get('get/notifications', [ApiController::class, 'get_notifications'])->name('get.notification');
    Route::post('mark-as-read', [ApiController::class, 'mark_as_read'])->name('mark.as.read');

    Route::get('get/user-status', [ApiController::class, 'user_status'])->name('get.user.status');
    Route::get('check/user-exists', [ApiController::class, 'user_exists'])->name('get.user.status');

});
Route::post('checkout', [CheckoutController::class, 'checkout']);