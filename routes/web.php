<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\SubscribersListController;
use App\Http\Controllers\SmtpSettingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\SmtpTestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});
// Public routes
Route::get('/unsubscribe/{token}', [UnsubscribeController::class, 'unsubscribe'])->name('unsubscribe');
Route::get('/tracking/open/{token}', [TrackingController::class, 'trackOpen'])->name('tracking.open');
Route::get('/tracking/click/{token}/{emailLogId}/{url}', [TrackingController::class, 'trackClick'])->name('tracking.click');

// Authenticated routes
Route::middleware(['auth'])
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Campaigns
        Route::resource('campaigns', CampaignController::class);
        Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');

        // Subscribers
        Route::resource('subscribers', SubscriberController::class);
        Route::get('/subscribers/import', [SubscriberController::class, 'import'])->name('subscribers.import');
        Route::post('/subscribers/import', [SubscriberController::class, 'importStore'])->name('subscribers.importStore');
        Route::get('/lists/{list}/export', [SubscriberController::class, 'export'])->name('subscribers.export');

        // Subscriber Lists
        Route::resource('lists', SubscribersListController::class);
        Route::post('/lists/{list}/toggle-status',[SubscribersListController::class, 'toggleStatus'])->name('subscriber-lists.toggle-status');

        // Email Templates
        Route::resource('templates', EmailTemplateController::class);
        Route::post('/templates/{template}/toggle-status',[EmailTemplateController::class, 'toggleStatus'])->name('templates.toggle-status');

        // SMTP Settings
        Route::get('/settings/smtp', [SmtpSettingController::class, 'index'])->name('settings.smtp.index');
        Route::post('/settings/smtp', [SmtpSettingController::class, 'store'])->name('settings.smtp.store');
        Route::post('/settings/smtp/test', [SmtpSettingController::class, 'test'])->name('settings.smtp.test');
    });
    Route::get('/admin/smtp/test', [SmtpTestController::class, 'sendTest']);

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
