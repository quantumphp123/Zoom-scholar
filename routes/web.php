<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InterestController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\TermsAndConditionsController;
use App\Http\Controllers\SocialController;

use App\Http\Middleware\Is_admin;
 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 //Privacy Policy
    
    Route::get('privacy-policy', [PrivacyPolicyController::class, 'privacyPolicy'])->name('privacy-policy');
    
Route::get('login/facebook', [SocialController::class,'facebookRedirect'])->name('facebookRedirect');
Route::get('facebook/callback', [SocialController::class,'facebookCallback'])->name('facebookCallback');
Route::get('facebook/token', [SocialController::class,'facebookToken'])->name('facebookToken');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/posts', function () {
    return view('admin.posts.index');
});


 
Auth::routes(['register' => false]);

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware'=>'auth'],function ()
{
   
Route::group(['middleware'=>'is_admin'],function ()
{
   Route::get('/home',[UserController::class, 'dashboard'])->name('admin.Dashboard');
     //Route::get('','Admin.Dashboard')->name('Admin.Dashboard');
    // User Routes
    Route::group(['prefix' => 'user'], function () { 
      Route::get('index', [UserController::class, 'Index'])->name('Admin.UserIndex');
      Route::post('change-status', [UserController::class, 'change_status'])->name('change-user-status');
      Route::get('delete/{id}', [UserController::class, 'delete'])->name('delete-user');
      Route::get('posts/get', [UserController::class, 'getUserPosts'])->name('posts.get');
      Route::get('questions/get', [UserController::class, 'getUserQuestions'])->name('question.get');
    });

    Route::prefix('profile')->group(function () {
        Route::get('get', [AdminController::class, 'getProfile'])->name('getProfile');
    });

    // Interest Routes
    Route::group(['prefix' => 'interest'], function () { 
      Route::get('index', [InterestController::class, 'Index'])->name('Admin.InterestIndex');
      Route::post('change-status', [InterestController::class, 'change_status'])->name('change-interest-status');
      Route::get('delete/{id}', [InterestController::class, 'delete'])->name('delete-interest');
      Route::post('edit', [InterestController::class, 'edit'])->name('edit-interest');
      Route::post('add', [InterestController::class, 'add'])->name('add-interest');
    });
    // Privacy Policy Routes
    Route::group(['prefix' => 'privacy-policy'], function () { 
      Route::get('index', [PrivacyPolicyController::class, 'Index'])->name('privacy-policy');
      Route::get('editor', [PrivacyPolicyController::class, 'editor'])->name('privacy-policy-editor');
      Route::post('save-privacy-policy', [PrivacyPolicyController::class, 'save'])->name('save-privacy-policy');
    });
    // About Us Routes
    Route::group(['prefix' => 'about-us'], function () { 
      Route::get('index', [AboutUsController::class, 'Index'])->name('about-us');
      Route::get('editor', [AboutUsController::class, 'editor'])->name('about-us-editor');
      Route::post('save-about-us', [AboutUsController::class, 'save'])->name('save-about-us');
    });
    // Terms And Conditions Routes
    Route::group(['prefix' => 'terms-and-conditions'], function () { 
      Route::get('index', [TermsAndConditionsController::class, 'Index'])->name('terms-and-conditions');
      Route::get('editor', [TermsAndConditionsController::class, 'editor'])->name('terms-and-conditions-editor');
      Route::post('save-terms-and-conditions', [TermsAndConditionsController::class, 'save'])->name('save-terms-and-conditions');
    });
    
   
    
});

});
 
  
   
