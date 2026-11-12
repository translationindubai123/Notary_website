<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController as AdminInquiryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', [AdminAuthController::class, 'create'])->middleware('guest')->name('login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->middleware(['guest', 'throttle:5,1'])->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->middleware('auth')->name('admin.logout');
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [AdminInquiryController::class, 'show'])->name('inquiries.show');
    Route::get('/settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    Route::get('/services/{service}/preview', [PublicSiteController::class, 'preview'])->name('services.preview');
});

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::redirect('/about', '/about-us/', 301);
Route::get('/about-us/', [PublicSiteController::class, 'about'])->name('about');
Route::get('/notary-services', [PublicSiteController::class, 'services'])->middleware('throttle:60,1')->name('services.index');
Route::get('/notary-services/category/{category}', [PublicSiteController::class, 'legacyCategory'])->name('services.category.legacy');
Route::get('/notary-services/service/{service}', [PublicSiteController::class, 'legacyService'])->name('services.show.legacy');
Route::get('/notary-services/{category}', [PublicSiteController::class, 'category'])->name('services.category');
Route::get('/notary-services/{category}/{service}', [PublicSiteController::class, 'service'])->name('services.show');
Route::get('/moj-services', [PublicSiteController::class, 'moj'])->name('moj');
Route::get('/moj-services/services', [PublicSiteController::class, 'mojServices'])->middleware('throttle:60,1')->name('moj.services.index');
Route::get('/moj-services/category/{category}', [PublicSiteController::class, 'legacyMojCategory'])->name('moj.services.category.legacy');
Route::get('/moj-services/service/{service}', [PublicSiteController::class, 'legacyMojService'])->name('moj.services.show.legacy');
Route::get('/moj-services/{category}', [PublicSiteController::class, 'mojCategory'])->name('moj.services.category');
Route::get('/moj-services/{category}/{service}', [PublicSiteController::class, 'mojService'])->name('moj.services.show');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/sitemap.xml', [PublicSiteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [PublicSiteController::class, 'robots'])->name('robots');
