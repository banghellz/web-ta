<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompleteProfileController;

// Controllers untuk Super Admin
use App\Http\Controllers\SuperAdmin\RfidTagController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\ItemController;
use App\Http\Controllers\SuperAdmin\LogPeminjamanController;

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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Login page
Route::get('/login', function () {
    return view('login');
})->name('login');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    // Google OAuth
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::any('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::prefix('guest')->name('guest.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Guest\DashboardController::class, 'index'])->name('dashboard.index');

    // AJAX endpoints
    Route::get('/tools/data', [App\Http\Controllers\Guest\DashboardController::class, 'getToolsData'])->name('tools.data');
    Route::get('/tools/check-updates', [App\Http\Controllers\Guest\DashboardController::class, 'checkUpdates'])->name('tools.check-updates');
    Route::get('/tools/stats', [App\Http\Controllers\Guest\DashboardController::class, 'getStats'])->name('tools.stats');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

// Profile completion (required after login)
Route::middleware(['auth'])->group(function () {
    Route::get('/complete-profile', [CompleteProfileController::class, 'index'])->name('user.complete-profile');
    Route::post('/complete-profile', [CompleteProfileController::class, 'store'])->name('user.complete-profile.store');
});

/*
|--------------------------------------------------------------------------
| Regular User Routes
|--------------------------------------------------------------------------
*/

Route::prefix('user')
    ->middleware(['auth', 'user'])
    ->name('user.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/refresh', [App\Http\Controllers\User\DashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::get('/dashboard/stats', [App\Http\Controllers\User\DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/dashboard/activities', [App\Http\Controllers\User\DashboardController::class, 'getRecentActivitiesAjax'])->name('dashboard.activities');
        Route::get('/dashboard/chart-data', [App\Http\Controllers\User\DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::post('/dashboard/sync-koin', [App\Http\Controllers\User\DashboardController::class, 'syncKoin'])->name('dashboard.sync-koin');

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\ProfileController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('update');
        });

        // Storage Management
        Route::prefix('storage')->name('storage.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\StorageController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\User\StorageController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\User\StorageController::class, 'getStats'])->name('stats');
            Route::get('/search', [App\Http\Controllers\User\StorageController::class, 'search'])->name('search');
            Route::get('/export', [App\Http\Controllers\User\StorageController::class, 'export'])->name('export');
            Route::get('/{id}', [App\Http\Controllers\User\StorageController::class, 'show'])->name('show');
            Route::patch('/{id}/return', [App\Http\Controllers\User\StorageController::class, 'returnItem'])->name('return');
        });

        // Log Peminjaman
        Route::prefix('log-peminjaman')->name('log-peminjaman.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\LogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\User\LogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\User\LogPeminjamanController::class, 'getStats'])->name('stats');
            Route::get('/currently-borrowed', [App\Http\Controllers\User\LogPeminjamanController::class, 'getCurrentBorrowed'])->name('currently_borrowed');
        });

        // Items Management
        Route::prefix('items')->name('items.')->group(function () {
            // CRUD Routes
            Route::get('/', [App\Http\Controllers\User\ItemController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\User\ItemController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\User\ItemController::class, 'store'])->name('store');
            Route::get('/{item}', [App\Http\Controllers\User\ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [App\Http\Controllers\User\ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [App\Http\Controllers\User\ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [App\Http\Controllers\User\ItemController::class, 'destroy'])->name('destroy');

            // AJAX Endpoints
            Route::get('/data', [App\Http\Controllers\User\ItemController::class, 'getData'])->name('data');
            Route::patch('/{item}/quantity', [App\Http\Controllers\User\ItemController::class, 'updateQuantity'])->name('update-quantity');
            Route::get('/check-updates', [App\Http\Controllers\User\ItemController::class, 'checkUpdates'])->name('check-updates');
            Route::get('/stats', [App\Http\Controllers\User\ItemController::class, 'getStats'])->name('stats');
        });

        // Missing Tools
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\MissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\User\MissingToolsController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\User\MissingToolsController::class, 'getStats'])->name('stats');
            Route::get('/search', [App\Http\Controllers\User\MissingToolsController::class, 'search'])->name('search');
        });
    });

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('superadmin')
    ->middleware(['auth', 'superadmin'])
    ->name('superadmin.')
    ->group(function () {

        // Redirect root to dashboard
        Route::any('/', function () {
            return redirect()->route('superadmin.dashboard.index');
        })->name('index');

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard.index');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            // CRUD Routes
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{uuid}', [UserController::class, 'show'])->name('show');
            Route::get('/{uuid}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{uuid}', [UserController::class, 'update'])->name('update');
            Route::delete('/{uuid}', [UserController::class, 'destroy'])->name('destroy');

            // AJAX Endpoints
            Route::get('/data', [UserController::class, 'getData'])->name('data');
            Route::get('/stats', [UserController::class, 'getStats'])->name('stats');
            Route::patch('/update-role', [UserController::class, 'updateRole'])->name('update-role');
            Route::post('/{uuid}/unassign-rfid', [UserController::class, 'unassignRfid'])->name('unassign-rfid');
            Route::get('/rfid/available', [UserController::class, 'getAvailableRfidTags'])->name('rfid.available');
            Route::get('/{uuid}/coin-info', [UserController::class, 'getCoinInfo'])->name('coin-info');
            Route::post('/{uuid}/sync-koin', [UserController::class, 'syncKoin'])->name('sync-koin');
        });

        // Activity Logs
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'getStats'])->name('stats');
            Route::post('/clear', [App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'clear'])->name('clear');
        });

        // RFID Tags Management
        Route::prefix('rfid-tags')->name('rfid-tags.')->group(function () {
            // CRUD Routes
            Route::get('/', [RfidTagController::class, 'index'])->name('index');
            Route::post('/', [RfidTagController::class, 'store'])->name('store');
            Route::get('/{rfidTag}', [RfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [RfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [RfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [RfidTagController::class, 'destroy'])->name('destroy');

            // AJAX Endpoints
            Route::get('/data', [RfidTagController::class, 'getData'])->name('data');
            Route::get('/stats', [RfidTagController::class, 'getStats'])->name('stats');
            Route::get('/available', [RfidTagController::class, 'getAvailable'])->name('available');
            Route::post('/release/{rfidTag}', [RfidTagController::class, 'releaseFromUser'])->name('release');
            Route::post('/bulk-status', [RfidTagController::class, 'bulkUpdateStatus'])->name('bulk-status');
        });

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('update');
        });

        // Items Management
        Route::prefix('items')->name('items.')->group(function () {
            // CRUD Routes
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/create', [ItemController::class, 'create'])->name('create');
            Route::post('/', [ItemController::class, 'store'])->name('store');
            Route::get('/{item}', [ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');

            // Soft Delete Management
            Route::post('/{id}/restore', [ItemController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [ItemController::class, 'forceDestroy'])->name('force-destroy');

            // Data Endpoints
            Route::get('/data/items', [ItemController::class, 'getData'])->name('data');
            Route::get('/data/deleted', [ItemController::class, 'getDeletedData'])->name('deleted-data');

            // Status & Validation
            Route::post('/{item}/change-status', [ItemController::class, 'changeStatus'])->name('change-status');
            Route::get('/check-epc', [ItemController::class, 'checkEpc'])->name('check-epc');
            Route::get('/check-updates', [ItemController::class, 'checkUpdates'])->name('check-updates');

            // Real-time Features
            Route::post('/force-refresh', [ItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::get('/stats', [ItemController::class, 'getStats'])->name('stats');
        });

        // Log Peminjaman
        Route::prefix('log_peminjaman')->name('log_peminjaman.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'dashboard'])->name('dashboard');
            Route::get('/export', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'export'])->name('export');
            Route::post('/clear', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'clear'])->name('clear');
            Route::get('/user/{userId}/history', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'userHistory'])->name('user.history');
            Route::get('/item/{itemId}/status', [App\Http\Controllers\SuperAdmin\LogPeminjamanController::class, 'checkItemStatus'])->name('item.status');
        });

        // Missing Tools
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'getData'])->name('data');
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'show'])->name('show');

            // Actions
            Route::post('/mark-missing/{itemId}', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'markAsMissing'])->name('mark-missing');
            Route::post('/{id}/reclaim', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'reclaimMissingItem'])->name('reclaim');
            Route::post('/{id}/cancel', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'cancelMissingTool'])->name('cancel');

            // API
            Route::get('/api/all', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'getAllMissingTools'])->name('api.all');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'index'])->name('index');
            Route::get('/count', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'getCount'])->name('count');
            Route::delete('/{id}', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'clearAll'])->name('clear-all');
        });
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

        // Redirect root to dashboard
        Route::any('/', function () {
            return redirect()->route('admin.dashboard.index');
        })->name('index');

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            // CRUD Routes
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            Route::get('/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::get('/{uuid}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
            Route::put('/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');

            // AJAX Endpoints
            Route::get('/data', [App\Http\Controllers\Admin\UserController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\Admin\UserController::class, 'getStats'])->name('stats');
            Route::patch('/update-role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('update-role');
            Route::post('/{uuid}/unassign-rfid', [App\Http\Controllers\Admin\UserController::class, 'unassignRfid'])->name('unassign-rfid');
            Route::get('/rfid/available', [App\Http\Controllers\Admin\UserController::class, 'getAvailableRfidTags'])->name('rfid.available');
            Route::get('/{uuid}/coin-info', [App\Http\Controllers\Admin\UserController::class, 'getCoinInfo'])->name('coin-info');
            Route::post('/{uuid}/sync-koin', [App\Http\Controllers\Admin\UserController::class, 'syncKoin'])->name('sync-koin');
        });

        // Activity Logs
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\ActivityLogController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\Admin\ActivityLogController::class, 'getStats'])->name('stats');
            Route::post('/clear', [App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('clear');
        });

        // RFID Tags Management
        Route::prefix('rfid-tags')->name('rfid-tags.')->group(function () {
            // CRUD Routes
            Route::get('/', [App\Http\Controllers\Admin\RfidTagController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\RfidTagController::class, 'store'])->name('store');
            Route::get('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [App\Http\Controllers\Admin\RfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'destroy'])->name('destroy');

            // AJAX Endpoints
            Route::get('/data', [App\Http\Controllers\Admin\RfidTagController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\Admin\RfidTagController::class, 'getStats'])->name('stats');
            Route::get('/available', [App\Http\Controllers\Admin\RfidTagController::class, 'getAvailable'])->name('available');
            Route::post('/release/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'releaseFromUser'])->name('release');
            Route::post('/bulk-status', [App\Http\Controllers\Admin\RfidTagController::class, 'bulkUpdateStatus'])->name('bulk-status');
        });

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
        });

        // Items Management
        Route::prefix('items')->name('items.')->group(function () {
            // CRUD Routes
            Route::get('/', [App\Http\Controllers\Admin\ItemController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\ItemController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\ItemController::class, 'store'])->name('store');
            Route::get('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [App\Http\Controllers\Admin\ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'destroy'])->name('destroy');

            // Soft Delete Management
            Route::post('/{id}/restore', [App\Http\Controllers\Admin\ItemController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [App\Http\Controllers\Admin\ItemController::class, 'forceDestroy'])->name('force-destroy');

            // Data Endpoints
            Route::get('/data/items', [App\Http\Controllers\Admin\ItemController::class, 'getData'])->name('data');
            Route::get('/data/deleted', [App\Http\Controllers\Admin\ItemController::class, 'getDeletedData'])->name('deleted-data');

            // Status & Validation
            Route::post('/{item}/change-status', [App\Http\Controllers\Admin\ItemController::class, 'changeStatus'])->name('change-status');
            Route::get('/check-epc', [App\Http\Controllers\Admin\ItemController::class, 'checkEpc'])->name('check-epc');
            Route::get('/check-updates', [App\Http\Controllers\Admin\ItemController::class, 'checkUpdates'])->name('check-updates');

            // Real-time Features
            Route::post('/force-refresh', [App\Http\Controllers\Admin\ItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::get('/stats', [App\Http\Controllers\Admin\ItemController::class, 'getStats'])->name('stats');
        });

        // Log Peminjaman
        Route::prefix('log_peminjaman')->name('log_peminjaman.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/dashboard', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'dashboard'])->name('dashboard');
            Route::get('/export', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'export'])->name('export');
            Route::post('/clear', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'clear'])->name('clear');
            Route::get('/user/{userId}/history', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'userHistory'])->name('user.history');
            Route::get('/item/{itemId}/status', [App\Http\Controllers\Admin\LogPeminjamanController::class, 'checkItemStatus'])->name('item.status');
        });

        // Missing Tools
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\MissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\MissingToolsController::class, 'getData'])->name('data');
            Route::get('/{id}', [App\Http\Controllers\Admin\MissingToolsController::class, 'show'])->name('show');

            // Actions
            Route::post('/mark-missing/{itemId}', [App\Http\Controllers\Admin\MissingToolsController::class, 'markAsMissing'])->name('mark-missing');
            Route::post('/{id}/reclaim', [App\Http\Controllers\Admin\MissingToolsController::class, 'reclaimMissingItem'])->name('reclaim');
            Route::post('/{id}/cancel', [App\Http\Controllers\Admin\MissingToolsController::class, 'cancelMissingTool'])->name('cancel');

            // API
            Route::get('/api/all', [App\Http\Controllers\Admin\MissingToolsController::class, 'getAllMissingTools'])->name('api.all');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('index');
            Route::get('/count', [App\Http\Controllers\Admin\NotificationController::class, 'getCount'])->name('count');
            Route::delete('/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])->name('clear-all');
        });
    });
