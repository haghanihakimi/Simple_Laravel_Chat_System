<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Environments\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Environments\FeedsController;
use App\Http\Controllers\Environments\IndividualMessagesController;
use App\Http\Controllers\Environments\SettingsController;

Auth::routes();

//Home Routes
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('landing');
    Route::get('/home', 'index')->name('home');
});

//Error logs
Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

//Login page
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/signin', 'signin')->name('signin');
    //Route::post('/user/log/sign/out', 'signout')->name('signout');
});

//Registration Page
Route::controller(RegisterController::class)->group(function () {
    Route::get('/signup', 'index')->name('registration');
    Route::post('/account/create', 'create')->name('create.account');
});

Auth::routes();

//Verification Routes
Route::controller(VerificationController::class)->group(function () {
    Route::get('/email/verify/{id}/{hash}', 'verification')->name('verification.verify');
    Route::get('/email/verify', 'view')->name('verification.notice');
    Route::post('/verification/resend', 'resend')->name('verification.resend');
});

//Feeds Routes
Route::controller(FeedsController::class)->group(function () {
    Route::get('/feeds', 'index')->name('feeds');
    Route::get('/messages', function () {
        return redirect()->route('feeds');
    });
});

//Individual Messages Routes
Route::controller(IndividualMessagesController::class)->group(function () {
    Route::get('/messages/{id}', 'index')->name('oneToOne.messages.id');
});

//Security Settings Routes
Route::controller(SettingsController::class)->group(function () {
    Route::get('/profile/security/settings', 'view')->name('security.settings');
});


//2-Factor Authentication Challenge
Route::controller(TwoFaController::class)->group(function () {
    Route::get('/user/two-factor-auth/challenge', 'challengeView')->name('twofa.challenge')->middleware(['guest']);
    Route::get('/user/two-factor-auth-challenge', 'confirmTwoFaChallenge')->name('two-factor.login')->middleware(['guest:'.config('fortify.guard')]);
});