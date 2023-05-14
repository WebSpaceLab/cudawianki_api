<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\InboxController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\OfferCategoryController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\SocialController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::get('/general', [GeneralController::class, 'index']);
Route::get('/meta', [MetaController::class, 'index']);
Route::get('/offers', [OfferController::class, 'get']);
Route::get('/about', [AboutController::class, 'get']);
// Route::get('/get-random-users', [GeneralController::class, 'getRandomUsers']);

Route::get('/social', [SocialController::class, 'index']);
Route::get('/contact', [ContactController::class, 'index']);
Route::post('/dashboard/inbox', [InboxController::class, 'store']);
Route::get('/categories', [OfferCategoryController::class, 'get']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logged-in-user', [UserController::class, 'loggedInUser']);
    Route::post('/update-user-image', [UserController::class, 'updateUserImage']);
    Route::patch('/update-user', [UserController::class, 'updateUser']);

    // Route::get('/profiles/{id}', [ProfileController::class, 'show']);

    /* MEDIA */
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/show', [MediaController::class, 'show']);
    Route::get('/media/search', [MediaController::class, 'search']);
    Route::post('/media', [MediaController::class, 'store']);
    Route::post('/media/with-cropper', [MediaController::class, 'storeWithCropper']);
    Route::put('/media/{media}/update', [MediaController::class, 'update']);
    Route::post('/media/{media}/update-file', [MediaController::class, 'updateFile']);
    Route::delete('/media', [MediaController::class, 'destroy']);

    /* General */
    Route::get('/general/show', [GeneralController::class, 'show']);
    Route::post('/general', [GeneralController::class, 'store']);
    Route::put('/general/{general}/update', [GeneralController::class, 'update']);
    Route::delete('/general', [GeneralController::class, 'destroy']);

    /* META */
    Route::get('/meta/show', [MetaController::class, 'show']);
    Route::post('/meta', [MetaController::class, 'store']);
    Route::put('/meta/{meta}/update', [MetaController::class, 'update']);
    Route::delete('/meta', [MetaController::class, 'destroy']);


    /* SOCIAL SOCIAL */
    Route::get('/social/show', [SocialController::class, 'show']);
    Route::post('/social', [SocialController::class, 'store']);
    Route::put('/social/{social}/update', [SocialController::class, 'update']);
    Route::delete('/social', [SocialController::class, 'destroy']);

    /* CONTACT */
    Route::get('/contact/show', [ContactController::class, 'show']);
    Route::post('/contact', [ContactController::class, 'store']);
    Route::put('/contact/{contact}/update', [ContactController::class, 'update']);
    Route::delete('/contact', [ContactController::class, 'destroy']);

    /* INBOX */
    Route::get('/dashboard/inbox', [InboxController::class, 'index']);
    Route::get('/dashboard/inbox/show', [InboxController::class, 'show']);
    Route::put('/dashboard/inbox/{mail}/update', [InboxController::class, 'update']);
    Route::put('/dashboard/inbox/{mail}/read', [InboxController::class, 'read']);
    Route::delete('/dashboard/inbox', [InboxController::class, 'destroy']);

    /* ABOUT */
    Route::get('/dashboard/about', [AboutController::class, 'index']);
    Route::post('/dashboard/about', [AboutController::class, 'store']);

    Route::put('/dashboard/about/{about}/update', [AboutController::class, 'update']);
    Route::put('/dashboard/about/{about}/read', [AboutController::class, 'active']);
    Route::delete('/dashboard/about', [AboutController::class, 'destroy']);

    /* Offer */
    Route::get('/dashboard/offers', [OfferController::class, 'index']);
    Route::post('/dashboard/offers', [OfferController::class, 'store']);
    Route::put('/dashboard/offer/{offer}/update', [OfferController::class, 'update']);
    Route::put('/dashboard/offer/{offer}/add-photos', [OfferController::class, 'addPhotosToOffer']);
    Route::delete('/dashboard/offer/{offer}/deleted-photo-from-offer', [OfferController::class, 'destroyPhotoFromOffer']);
    Route::delete('/dashboard/offer/{offer}/deleted', [OfferController::class, 'destroy']);


    /* Offer Categories */
    Route::get('/offers/categories', [OfferCategoryController::class, 'index']);
    Route::post('/offers/categories', [OfferCategoryController::class, 'store']);
    Route::get('/offers/categories/create', [OfferCategoryController::class, 'create']);
    Route::put('/offers/category/{offer_category}/update', [OfferCategoryController::class, 'update']);
    Route::put('/offers/category/{offer_category}/add-photos', [OfferCategoryController::class, 'addPhotosToCategory']);
    Route::delete('/offers/category/{offer_category}/deleted-photo-from-category', [OfferCategoryController::class, 'destroyPhotoFromCategory']);
    Route::delete('/offers/category', [OfferCategoryController::class, 'destroy']);
});
