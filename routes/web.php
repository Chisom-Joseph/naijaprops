<?php

use App\Http\Controllers\Admin\PropertyManagementController;
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavedPropertyController;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Home Page
Route::get('/', function (Request $request) {
    $featuredProperties = Property::with(['primaryImage', 'owner'])
        ->withCount('images')
        ->where('status', 'available')
        ->latest()
        ->take(6)
        ->get();

    $savedPropertyIds = $request->user()
        ? $request->user()->savedProperties()->pluck('property_id')->all()
        : [];

    return view('welcome', compact('featuredProperties', 'savedPropertyIds'));
})->name('home');

// Public Property Browsing Routes (static auth routes defined below before {property})
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Authenticated User Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Role-aware User Dashboard (Owners, Agents, Buyers/Tenants)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Saved Properties Routes
    Route::get('/saved-properties', [SavedPropertyController::class, 'index'])->name('saved-properties.index');
    Route::post('/properties/{property}/save', [SavedPropertyController::class, 'toggle'])->whereNumber('property')->name('saved-properties.toggle');
    Route::delete('/saved-properties/{property}', [SavedPropertyController::class, 'destroy'])->whereNumber('property')->name('saved-properties.destroy');

    // Property Enquiries Routes
    Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::post('/properties/{property}/enquiries', [EnquiryController::class, 'store'])->whereNumber('property')->name('enquiries.store');
    Route::patch('/enquiries/{enquiry}/status', [EnquiryController::class, 'updateStatus'])->whereNumber('enquiry')->name('enquiries.update-status');
    Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->whereNumber('enquiry')->name('enquiries.destroy');

    // Suspicious Property Reporting Route
    Route::post('/properties/{property}/reports', [ReportController::class, 'store'])->whereNumber('property')->name('reports.store');

    // Property Management Routes (Owners, Agents, Admins enforced via PropertyPolicy)
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/my-listings', [PropertyController::class, 'myListings'])->name('my');
        Route::get('/create', [PropertyController::class, 'create'])->name('create');
        Route::post('/', [PropertyController::class, 'store'])->name('store');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->whereNumber('property')->name('edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->whereNumber('property')->name('update');
        Route::patch('/{property}/status', [PropertyController::class, 'updateStatus'])->whereNumber('property')->name('update-status');
        Route::delete('/{property}/images/{propertyImage}', [PropertyController::class, 'destroyImage'])->whereNumber(['property', 'propertyImage'])->name('images.destroy');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->whereNumber('property')->name('destroy');
    });

    // Admin Routes (Role: admin)
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [UserManagementController::class, 'dashboard'])->name('dashboard');
            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');

            Route::get('/properties', [PropertyManagementController::class, 'index'])->name('properties.index');
            Route::patch('/properties/{property}/approve', [PropertyManagementController::class, 'approve'])->whereNumber('property')->name('properties.approve');
            Route::patch('/properties/{property}/reject', [PropertyManagementController::class, 'reject'])->whereNumber('property')->name('properties.reject');

            Route::get('/reports', [ReportManagementController::class, 'index'])->name('reports.index');
            Route::patch('/reports/{report}/status', [ReportManagementController::class, 'updateStatus'])->whereNumber('report')->name('reports.update-status');
            Route::delete('/reports/{report}', [ReportManagementController::class, 'destroy'])->whereNumber('report')->name('reports.destroy');
        });
});

// Public Single Property Details Route
Route::get('/properties/{property}', [PropertyController::class, 'show'])
    ->whereNumber('property')
    ->name('properties.show');
