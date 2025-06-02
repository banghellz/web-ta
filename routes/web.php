<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\RfidTagController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ItemController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\SuperAdmin\LogPeminjamanController;
use App\Livewire\UsersTable;

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

// Route homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

// Auth Routes
Route::prefix('auth')->group(function () {
    // Google Login Routes
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Logout Route
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// PERBAIKAN: Tambahkan route untuk guest dashboard
Route::prefix('guest')
    ->middleware(['auth'])
    ->name('guest.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Guest\DahsboardController::class, 'index'])->name('dashboard.index');
    });

// Profile completion routes - PERBAIKAN: Tambahkan middleware auth
Route::middleware(['auth'])->group(function () {
    Route::get('/complete-profile', [CompleteProfileController::class, 'index'])->name('user.complete-profile');
    Route::post('/complete-profile', [CompleteProfileController::class, 'store'])->name('user.complete-profile.store');
});

// User routes
Route::prefix('user')
    ->middleware(['auth', 'user'])
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard.index');
    });

// Super Admin routes
Route::prefix('superadmin')
    ->middleware(['auth', 'superadmin'])
    ->name('superadmin.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
            ->name('dashboard.index');

        // User management routes
        Route::get('/users', [App\Http\Controllers\SuperAdmin\UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/data', [App\Http\Controllers\SuperAdmin\UserController::class, 'getData'])
            ->name('users.data');

        Route::patch('/users/update-role', [App\Http\Controllers\SuperAdmin\UserController::class, 'updateRole'])
            ->name('users.update-role');

        Route::get('/users/{uuid}', [App\Http\Controllers\SuperAdmin\UserController::class, 'show'])
            ->name('users.show');

        Route::get('/users/{uuid}/edit', [App\Http\Controllers\SuperAdmin\UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{uuid}', [App\Http\Controllers\SuperAdmin\UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{uuid}', [App\Http\Controllers\SuperAdmin\UserController::class, 'destroy'])
            ->name('users.destroy');

        // RFID Tag assignment routes (for users)
        Route::get('/available-rfid-tags', [App\Http\Controllers\SuperAdmin\UserController::class, 'getAvailableRfidTags'])
            ->name('available-rfid-tags');

        Route::post('/users/{uuid}/unassign-rfid', [App\Http\Controllers\SuperAdmin\UserController::class, 'unassignRfid'])
            ->name('users.unassign-rfid');

        // Activity logs routes
        Route::get('/activity-logs', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])
            ->name('activity-logs.index');

        Route::get('/activity-logs/data', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'getData'])
            ->name('activity-logs.data');

        Route::post('/activity-logs/clear', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'clear'])
            ->name('activity-logs.clear');

        Route::get('/activity-logs/stats', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'getStats'])
            ->name('activity-logs.stats');

        // RFID Tags management routes
        Route::prefix('rfid-tags')->name('rfid-tags.')->group(function () {
            // Main RFID tags page
            Route::get('/', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'index'])->name('index');

            // DataTables data endpoint
            Route::get('/data', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'getData'])->name('data');

            // Get statistics
            Route::get('/stats', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'getStats'])->name('stats');

            // Get available RFID tags
            Route::get('/available', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'getAvailable'])->name('available');

            // Bulk operations
            Route::post('/bulk-update-status', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

            // CRUD operations
            Route::post('/', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'store'])->name('store');
            Route::get('/{rfidTag}', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [App\Http\Controllers\SuperAdmin\RfidTagController::class, 'destroy'])->name('destroy');
        });

        // Items management routes
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\ItemController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\SuperAdmin\ItemController::class, 'getData'])->name('data');
            Route::get('/create', [App\Http\Controllers\SuperAdmin\ItemController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\SuperAdmin\ItemController::class, 'store'])->name('store');
            Route::get('/{item}', [App\Http\Controllers\SuperAdmin\ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [App\Http\Controllers\SuperAdmin\ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [App\Http\Controllers\SuperAdmin\ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [App\Http\Controllers\SuperAdmin\ItemController::class, 'destroy'])->name('destroy');
            Route::patch('/{item}/quantity', [App\Http\Controllers\SuperAdmin\ItemController::class, 'updateQuantity'])->name('update-quantity');
        });

        Route::prefix('log_peminjaman')->name('log_peminjaman.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/export', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'export'])->name('export');
            Route::post('/clear', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'clear'])->name('clear');
            Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'dashboard'])->name('dashboard');
            Route::get('/user/{userId}/history', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'userHistory'])->name('user.history');
            Route::get('/item/{itemId}/status', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'checkItemStatus'])->name('item.status');
        });
    });

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard.index');

        // User management routes
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/data', [App\Http\Controllers\Admin\UserController::class, 'getData'])
            ->name('users.data');

        Route::patch('/users/update-role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])
            ->name('users.update-role');

        Route::get('/users/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'show'])
            ->name('users.show');

        Route::get('/users/{uuid}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])
            ->name('users.destroy');

        // RFID Tag assignment routes (for users)
        Route::get('/available-rfid-tags', [App\Http\Controllers\Admin\UserController::class, 'getAvailableRfidTags'])
            ->name('available-rfid-tags');

        Route::post('/users/{uuid}/unassign-rfid', [App\Http\Controllers\Admin\UserController::class, 'unassignRfid'])
            ->name('users.unassign-rfid');

        // Activity logs routes
        Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])
            ->name('activity-logs.index');

        Route::get('/activity-logs/data', [App\Http\Controllers\Admin\ActivityLogController::class, 'getData'])
            ->name('activity-logs.data');

        Route::post('/activity-logs/clear', [App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])
            ->name('activity-logs.clear');

        Route::get('/activity-logs/stats', [App\Http\Controllers\Admin\ActivityLogController::class, 'getStats'])
            ->name('activity-logs.stats');

        // RFID Tags management routes
        Route::prefix('rfid-tags')->name('rfid-tags.')->group(function () {
            // Main RFID tags page
            Route::get('/', [App\Http\Controllers\Admin\RfidTagController::class, 'index'])->name('index');

            // DataTables data endpoint
            Route::get('/data', [App\Http\Controllers\Admin\RfidTagController::class, 'getData'])->name('data');

            // Get statistics
            Route::get('/stats', [App\Http\Controllers\Admin\RfidTagController::class, 'getStats'])->name('stats');

            // Get available RFID tags
            Route::get('/available', [App\Http\Controllers\Admin\RfidTagController::class, 'getAvailable'])->name('available');

            // Bulk operations
            Route::post('/bulk-update-status', [App\Http\Controllers\Admin\RfidTagController::class, 'bulkUpdateStatus'])->name('bulk-update-status');

            // CRUD operations
            Route::post('/', [App\Http\Controllers\Admin\RfidTagController::class, 'store'])->name('store');
            Route::get('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [App\Http\Controllers\Admin\RfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'destroy'])->name('destroy');
        });

        // Items management routes
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ItemController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\ItemController::class, 'getData'])->name('data');
            Route::get('/create', [App\Http\Controllers\Admin\ItemController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\ItemController::class, 'store'])->name('store');
            Route::get('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [App\Http\Controllers\Admin\ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'destroy'])->name('destroy');
            Route::patch('/{item}/quantity', [App\Http\Controllers\Admin\ItemController::class, 'updateQuantity'])->name('update-quantity');
        });

        Route::prefix('log_peminjaman')->name('log_peminjaman.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/export', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'export'])->name('export');
            Route::post('/clear', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'clear'])->name('clear');
            Route::get('/dashboard', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'dashboard'])->name('dashboard');
            Route::get('/user/{userId}/history', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'userHistory'])->name('user.history');
            Route::get('/item/{itemId}/status', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'checkItemStatus'])->name('item.status');
        });
    });
