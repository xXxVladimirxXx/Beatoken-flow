<?php

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

Route::group(['namespace' => 'App\Http\Controllers\Api'], function () {

    Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify');
    Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');

    /* For unauthorized users */
    Route::post('login', 'UserController@authenticate')->name('users.authenticate');
    Route::post('register', 'UserController@register')->name('users.register');
    Route::get('forgot-password/{email}', 'UserController@forgotPassword')->name('forgotPassword');
    Route::post('check-token', 'UserController@checkToken')->name('checkToken');
    Route::post('reset/process', 'UserController@setNewPassword')->name('setNewPassword');

    /* For authorized users */
    Route::group(['middleware' => ['auth:api', 'verified']], function () {

        /* Settings */
        Route::apiResource('settings', 'SettingController')->except('update', 'destroy');

        /* Users */
        Route::get('users/current-user', 'UserController@currentUser')->name('users.currentUser');
        Route::get('users/get-middleman-user', 'UserController@getMiddlemanUser')->name('users.getMiddlemanUser');
        Route::get('users/get-withdraw-manager-user', 'UserController@getWithdrawManagerUser')->name('users.getWithdrawManagerUser');
        Route::post('users/change-avatar/{user}', 'UserController@changeAvatar')->name('users.changeAvatar');
        Route::post('users/change-password/{user}', 'UserController@changePassword')->name('users.changePassword');
        Route::post('users/refill-balance/{user}', 'UserController@refillBalance')->name('users.refillBalance')->middleware('role:admin');
        Route::apiResource('users', 'UserController')->except('store', 'destroy');

        /* Payments */
        Route::post('payments/through-stripe-gateway', 'PaymentController@throughStripeGateway')->name('payments.throughStripeGateway');
        Route::post('payments/update-payment-refill-balance', 'PaymentController@updatePaymentRefillBalance')->name('payments.updatePaymentRefillBalance');

        /* Withdrawalrequests */
        Route::apiResource('withdrawal-requests', 'WithdrawalRequestController')->except('destroy');

        /* Roles */
        Route::apiResource('roles', 'RoleController')->except('show', 'store', 'update', 'destroy');

        /* FlowAccounts */
        Route::get('flow-accounts/get-by-address/{address}', 'FlowAccountController@getByAddress')->name('flow-accounts.getByAddress');
        Route::apiResource('flow-accounts', 'FlowAccountController')->except('update', 'destroy');

        /* Marketplace */
        Route::post('marketplace/set-price/{nft}', 'MarketplaceController@setPrice')->name('marketplace.setPrice');
        Route::post('marketplace/transfer-nft-to-me/{nft}', 'MarketplaceController@transferNftToMe')->name('marketplace.transferNftToMe');

        /* Categories */
        Route::get('categories/get-packs', 'CategoryController@getPacks')->name('categories.getPacks');
        Route::post('categories/create-set', 'CategoryController@createSet')->name('categories.createSet');
        Route::post('categories/create-release', 'CategoryController@createRelease')->name('categories.createRelease');
        Route::post('categories/create-pack', 'CategoryController@createPack')->name('categories.createPack');
        Route::apiResource('categories', 'CategoryController')->except('show', 'store', 'update', 'destroy');

        /* Nfts */
        Route::get('nfts/nfts-for-sale', 'NftController@getNftsForSale')->name('nfts.getNftsForSale');
        Route::get('nfts/all-nfts-of-all-users', 'NftController@allNftsOfAllUsers')->name('nfts.allNftsOfAllUsers');
        Route::get('nfts/by-user-id/{user_id}', 'NftController@allNftsByUserId')->name('nfts.allNftsByUserId');
        Route::get('nfts/send-as-gift/{nft}/{address}', 'NftController@sendNftAsGift')->name('nfts.sendNftAsGift');
        Route::post('nfts/synchronization-nfts', 'NftController@synchronizationNfts')->name('nfts.synchronizationNfts');
        Route::apiResource('nfts', 'NftController')->except('update', 'destroy');

        /* Drops */
        Route::get('drops/by-user-id', 'DropController@allDropsByUser')->name('drops.allDropsByUser');
        Route::get('drops/get-current-drop', 'DropController@getCurrentDrop')->name('drops.getCurrentDrop');
        Route::post('drops/buy-drop/{drop}', 'DropController@buyDrop')->name('drops.buyDrop');
        Route::apiResource('drops', 'DropController');

        /* Drop lines */
        Route::get('drop-lines/my-time-to-start-buy-drop/{drop_id}', 'DropLineController@myTimeToStartBuyDrop')->name('drop-line.myTimeToStartBuyDrop');
        Route::get('drop-lines/my-time-to-buy-drop/{drop_id}', 'DropLineController@myTimeToBuyDrop')->name('drop-line.myTimeToBuyDrop');
        Route::get('drop-lines/store-by-drop/{drop}', 'DropLineController@storeByDrop')->name('drop-line.storeByDrop');
        Route::get('drop-lines/destroy-by-drop/{drop_id}', 'DropLineController@destroyByDrop')->name('drop-line.destroyByDrop');

        /* Transactions history */
        Route::get('transactions-history/by-user-id/{user_id}', 'TransactionHistoryController@getByUserId')->name('transactions-history.getByUserId');
        Route::apiResource('transactions-history', 'TransactionHistoryController')->except('index', 'store', 'update');
    });
});
