<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Environments\IndividualMessagesController;
use App\Http\Controllers\Environments\SettingsController;
use App\Http\Controllers\Environments\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFaController;
use App\Http\Controllers\Environments\ContactsController;
use App\Http\Controllers\Environments\ConversationsController;
use App\Http\Controllers\Auth\ResetPasswordController;

//Signout API
Route::controller(LoginController::class)->group(function () {
    Route::post('/user/log/sign/out', 'signout')->name('signout');
});

//Messaging API Routes
Route::controller(IndividualMessagesController::class)->group(function () {
    Route::get('/messages/fetch/data/messages/{user}', 'fetchMessages');
    Route::get('/messages/fetch/data/messages/recent/{user}', 'fetchRecentMessage');
    Route::post('/messages/send/new/{user}', 'sendMessage');
});

//Preferences API Routes
Route::controller(SettingsController::class)->group(function () {
    Route::get('/preferences/collect', 'collectPreferences');
    Route::post('/preferences/update/dark/mode', 'updateDarkMode');
    Route::post('/preferences/update/notifications/sound', 'updateNotificationSound');
    Route::post('/preferences/update/messages/sound', 'updateMessageSound');
});

//User Profiles Routes
Route::controller(UserController::class)->group(function () {
    Route::get('/profiles/collect', 'collectUserData');
    Route::post('/profiles/update', 'saveChnages');
});

//Search Engine Routes
Route::controller(UserController::class)->group(function () {
    Route::get('/peopleSearch', 'collectPeopleSearch');
    Route::get('/collect/searched/user/info/{id}', 'collectSearchedUser');
});

//Contacts Routes
Route::controller(ContactsController::class)->group(function () {
    Route::get('/prepare/list/contacts', 'listContacts');
    Route::get('/fetch/contact/received/pending/requests', 'receivedPendingContacts');
    Route::get('/fetch/contact/sent/pending/requests', 'sentPendingContacts');
    Route::post('/send/contact/request/{id}', 'sendRequest');
    Route::post('/send/contact/cancelation/{id}', 'cancelRequest');
    Route::post('/send/contact/reject/{id}', 'rejectRequest');
    Route::post('/send/contact/approve/{id}', 'acceptRequest');
    Route::post('/send/contact/remove/{id}', 'removeContact');
    Route::post('/send/contact/block/{id}', 'blockContact');
    Route::post('/send/contact/unblock/{id}', 'unBlockContact');
    Route::post('/send/contact/mark/spam/{id}', 'markedContactAsSpam');
    Route::post('/send/contact/unmark/spam/{id}', 'contactUnmarkedSpam');
});

//Security Settings Routes
Route::controller(SettingsController::class)->group(function () {
    Route::get('/user/settings/security/profile', 'getUser');
    Route::post('/user/update/security/settings/changes', 'saveChanges');
    Route::post('/users/accounts/current/delele/perm', 'accountDelete');
});

//Two Factor Authentication Routes
Route::controller(TwoFaController::class)->group(function () {
    Route::post('/user/check/chk-confirm-password', 'confirmPassword')->middleware(['auth']); //requires user's password as ONE input
    Route::get('/user/check/chk-password-status', 'passwordStatus')->middleware(['auth']); //check if entered password is valid/correct
    Route::post('/user/enable/act-two-factor-auth', 'enableTwoFa')->middleware(['auth']); //enables two factor authentication
    Route::post('/user/disable/deact-two-factor-auth', 'disableTwoFa')->middleware(['auth']); //disables two factor authentication
    Route::get('/user/twofa/twofa-qr-code', 'showQrCodeSvg')->middleware(['auth']); //displays two factor authentication code as QR Code in SVG format
    Route::get('/user/twofa/twofa-recovery-codes', 'showRecoverCodes')->middleware(['auth']); //displays two factor authentication Recover Codes
});

//Password Reset Routes
Route::controller(ResetPasswordController::class)->group(function () {
    Route::post('/auth/forgot-password', 'authSendLink')->middleware(['auth', 'throttle:3,5']);
    Route::post('/guest/forgot-password', 'guestSendLink')->middleware(['guest', 'throttle:3,5']);
});

//Conversations Routes
Route::controller(ConversationsController::class)->group(function () {
    Route::get('/chats/conversation/pull', 'getConversations')->middleware(['auth', 'verified']);
    Route::post('/chats/conversation/delete', 'deleteConversation')->middleware('auth', 'verified');
});