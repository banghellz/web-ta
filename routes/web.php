<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompleteProfileController;

// SuperAdmin Controllers
use App\Http\Controllers\SuperAdmin\RfidTagController as SuperAdminRfidTagController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\ItemController as SuperAdminItemController;
use App\Http\Controllers\SuperAdmin\LogPeminjamanController as SuperAdminLogPeminjamanController;
use App\Http\Controllers\SuperAdmin\MissingToolsController as SuperAdminMissingToolsController;
use App\Http\Controllers\SuperAdmin\NotificationController as SuperAdminNotificationController;
use App\Http\Controllers\SuperAdmin\ActivityLogController as SuperAdminActivityLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ProfileController as SuperAdminProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\RfidTagController as AdminRfidTagController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\Admin\LogPeminjamanController as AdminLogPeminjamanController;
use App\Http\Controllers\Admin\MissingToolsController as AdminMissingToolsController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\StorageController as UserStorageController;
use App\Http\Controllers\User\LogPeminjamanController as UserLogPeminjamanController;
use App\Http\Controllers\User\ItemController as UserItemController;
use App\Http\Controllers\User\MissingToolsController as UserMissingToolsController;

// Guest Controllers
use App\Http\Controllers\Guest\DahsboardController as GuestDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// PUBLIC ROUTES
// ==========================================

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

// ==========================================
// AUTHENTICATION ROUTES
// ==========================================

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->name('google');
    Route::any('/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==========================================
// AUTHENTICATED ROUTES
// ==========================================

Route::middleware(['auth'])->group(function () {

    // Profile completion routes
    Route::get('/complete-profile', [CompleteProfileController::class, 'index'])->name('user.complete-profile');
    Route::post('/complete-profile', [CompleteProfileController::class, 'store'])->name('user.complete-profile.store');

    // ==========================================
    // GUEST ROUTES
    // ==========================================

    Route::prefix('guest')->name('guest.')->group(function () {
        Route::get('/dashboard', [GuestDashboardController::class, 'index'])->name('dashboard.index');
    });
});

// ==========================================
// USER ROUTES
// ==========================================

Route::prefix('user')
    ->middleware(['auth', 'user'])
    ->name('user.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/refresh', [UserDashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::get('/dashboard/stats', [UserDashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/dashboard/activities', [UserDashboardController::class, 'getRecentActivitiesAjax'])->name('dashboard.activities');
        Route::get('/dashboard/chart-data', [UserDashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::post('/dashboard/sync-koin', [UserDashboardController::class, 'syncKoin'])->name('dashboard.sync-koin');

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [UserProfileController::class, 'index'])->name('index');
            Route::put('/', [UserProfileController::class, 'update'])->name('update');
        });

        // Storage Management
        Route::prefix('storage')->name('storage.')->group(function () {
            Route::get('/', [UserStorageController::class, 'index'])->name('index');
            Route::get('/data', [UserStorageController::class, 'getData'])->name('data');
            Route::get('/stats', [UserStorageController::class, 'getStats'])->name('stats');
            Route::get('/search', [UserStorageController::class, 'search'])->name('search');
            Route::get('/export', [UserStorageController::class, 'export'])->name('export');
            Route::get('/{id}', [UserStorageController::class, 'show'])->name('show');
            Route::patch('/{id}/return', [UserStorageController::class, 'returnItem'])->name('return');
        });

        // Log Peminjaman
        Route::prefix('log-peminjaman')->name('log-peminjaman.')->group(function () {
            Route::get('/', [UserLogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [UserLogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/stats', [UserLogPeminjamanController::class, 'getStats'])->name('stats');
            Route::get('/currently-borrowed', [UserLogPeminjamanController::class, 'getCurrentBorrowed'])->name('currently_borrowed');
        });

        // Items Management (with Real-time Cache)
        Route::prefix('items')->name('items.')->group(function () {
            // Basic CRUD
            Route::get('/', [UserItemController::class, 'index'])->name('index');
            Route::get('/data', [UserItemController::class, 'getData'])->name('data');
            Route::get('/create', [UserItemController::class, 'create'])->name('create');
            Route::post('/', [UserItemController::class, 'store'])->name('store');
            Route::get('/{item}', [UserItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [UserItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [UserItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [UserItemController::class, 'destroy'])->name('destroy');
            Route::patch('/{item}/quantity', [UserItemController::class, 'updateQuantity'])->name('update-quantity');

            // Real-time & Cache Management
            Route::get('/check-updates', [UserItemController::class, 'checkUpdates'])->name('check-updates');
            Route::get('/stats', [UserItemController::class, 'getStats'])->name('stats');
            Route::post('/force-refresh', [UserItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::post('/clear-cache', [UserItemController::class, 'clearUserCaches'])->name('clear-cache');
            Route::post('/warm-cache', [UserItemController::class, 'warmCache'])->name('warm-cache');
            Route::get('/cache-info', [UserItemController::class, 'getCacheInfo'])->name('cache-info');
        });

        // Missing Tools
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [UserMissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [UserMissingToolsController::class, 'getData'])->name('data');
            Route::get('/stats', [UserMissingToolsController::class, 'getStats'])->name('stats');
            Route::get('/search', [UserMissingToolsController::class, 'search'])->name('search');
            Route::get('/export', [UserMissingToolsController::class, 'export'])->name('export');
        });
    });

// ==========================================
// SUPERADMIN ROUTES
// ==========================================

Route::prefix('superadmin')
    ->middleware(['auth', 'superadmin'])
    ->name('superadmin.')
    ->group(function () {

        // Dashboard
        Route::any('/', fn() => redirect()->route('superadmin.dashboard.index'))->name('index');
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard.index');

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [SuperAdminProfileController::class, 'index'])->name('index');
            Route::put('/', [SuperAdminProfileController::class, 'update'])->name('update');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [SuperAdminUserController::class, 'index'])->name('index');
            Route::get('/data', [SuperAdminUserController::class, 'getData'])->name('data');
            Route::get('/create', [SuperAdminUserController::class, 'create'])->name('create');
            Route::post('/', [SuperAdminUserController::class, 'store'])->name('store');
            Route::get('/{uuid}', [SuperAdminUserController::class, 'show'])->name('show');
            Route::get('/{uuid}/edit', [SuperAdminUserController::class, 'edit'])->name('edit');
            Route::put('/{uuid}', [SuperAdminUserController::class, 'update'])->name('update');
            Route::delete('/{uuid}', [SuperAdminUserController::class, 'destroy'])->name('destroy');

            // AJAX endpoints
            Route::patch('/update-role', [SuperAdminUserController::class, 'updateRole'])->name('update-role');
            Route::post('/{uuid}/unassign-rfid', [SuperAdminUserController::class, 'unassignRfid'])->name('unassign-rfid');
            Route::get('/rfid/available', [SuperAdminUserController::class, 'getAvailableRfidTags'])->name('rfid.available');
            Route::get('/stats', [SuperAdminUserController::class, 'getStats'])->name('stats');
            Route::get('/{uuid}/coin-info', [SuperAdminUserController::class, 'getCoinInfo'])->name('coin-info');
            Route::post('/{uuid}/sync-koin', [SuperAdminUserController::class, 'syncKoin'])->name('sync-koin');
        });

        // RFID Tags Management
        Route::prefix('rfid-tags')->name('rfid-tags.')->group(function () {
            Route::get('/', [SuperAdminRfidTagController::class, 'index'])->name('index');
            Route::get('/data', [SuperAdminRfidTagController::class, 'getData'])->name('data');
            Route::post('/', [SuperAdminRfidTagController::class, 'store'])->name('store');
            Route::get('/{rfidTag}', [SuperAdminRfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [SuperAdminRfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [SuperAdminRfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [SuperAdminRfidTagController::class, 'destroy'])->name('destroy');

            // AJAX endpoints
            Route::get('/stats', [SuperAdminRfidTagController::class, 'getStats'])->name('stats');
            Route::get('/available', [SuperAdminRfidTagController::class, 'getAvailable'])->name('available');
            Route::post('/release/{rfidTag}', [SuperAdminRfidTagController::class, 'releaseFromUser'])->name('release');
            Route::post('/bulk-status', [SuperAdminRfidTagController::class, 'bulkUpdateStatus'])->name('bulk-status');
        });

        // Items Management (with Enhanced Cache)
        Route::prefix('items')->name('items.')->group(function () {
            // Basic CRUD
            Route::get('/', [SuperAdminItemController::class, 'index'])->name('index');
            Route::get('/create', [SuperAdminItemController::class, 'create'])->name('create');
            Route::post('/', [SuperAdminItemController::class, 'store'])->name('store');
            Route::get('/{item}', [SuperAdminItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [SuperAdminItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [SuperAdminItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [SuperAdminItemController::class, 'destroy'])->name('destroy');

            // Soft Delete Management
            Route::post('/{id}/restore', [SuperAdminItemController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [SuperAdminItemController::class, 'forceDestroy'])->name('force-destroy');

            // Data endpoints
            Route::get('/data/items', [SuperAdminItemController::class, 'getData'])->name('data');
            Route::get('/data/deleted', [SuperAdminItemController::class, 'getDeletedData'])->name('deleted-data');

            // Status & Validation
            Route::post('/{item}/change-status', [SuperAdminItemController::class, 'changeStatus'])->name('change-status');
            Route::get('/check-epc', [SuperAdminItemController::class, 'checkEpc'])->name('check-epc');

            // Real-time & Cache Management
            Route::get('/check-updates', [SuperAdminItemController::class, 'checkUpdates'])->name('check-updates');
            Route::get('/stats', [SuperAdminItemController::class, 'getStats'])->name('stats');
            Route::post('/force-refresh', [SuperAdminItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::post('/force-refresh-all', [SuperAdminItemController::class, 'forceRefreshAll'])->name('force-refresh-all');
            Route::post('/warm-all-caches', [SuperAdminItemController::class, 'warmAllCaches'])->name('warm-all-caches');
            Route::get('/cache-status', [SuperAdminItemController::class, 'getCacheStatus'])->name('cache-status');
        });

        // Log Peminjaman
        Route::prefix('log_peminjaman')->name('log_peminjaman.')->group(function () {
            Route::get('/', [SuperAdminLogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [SuperAdminLogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/export', [SuperAdminLogPeminjamanController::class, 'export'])->name('export');
            Route::post('/clear', [SuperAdminLogPeminjamanController::class, 'clear'])->name('clear');
            Route::get('/dashboard', [SuperAdminLogPeminjamanController::class, 'dashboard'])->name('dashboard');
            Route::get('/user/{userId}/history', [SuperAdminLogPeminjamanController::class, 'userHistory'])->name('user.history');
            Route::get('/item/{itemId}/status', [SuperAdminLogPeminjamanController::class, 'checkItemStatus'])->name('item.status');
        });

        // Missing Tools
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [SuperAdminMissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [SuperAdminMissingToolsController::class, 'getData'])->name('data');
            Route::get('/{id}', [SuperAdminMissingToolsController::class, 'show'])->name('show');

            // Actions
            Route::post('/mark-missing/{itemId}', [SuperAdminMissingToolsController::class, 'markAsMissing'])->name('mark-missing');
            Route::post('/{id}/reclaim', [SuperAdminMissingToolsController::class, 'reclaimMissingItem'])->name('reclaim');
            Route::post('/{id}/cancel', [SuperAdminMissingToolsController::class, 'cancelMissingTool'])->name('cancel');
            Route::get('/api/all', [SuperAdminMissingToolsController::class, 'getAllMissingTools'])->name('api.all');
        });

        // Activity Logs
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [SuperAdminActivityLogController::class, 'index'])->name('index');
            Route::get('/data', [SuperAdminActivityLogController::class, 'getData'])->name('data');
            Route::post('/clear', [SuperAdminActivityLogController::class, 'clear'])->name('clear');
            Route::get('/stats', [SuperAdminActivityLogController::class, 'getStats'])->name('stats');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [SuperAdminNotificationController::class, 'index'])->name('index');
            Route::get('/count', [SuperAdminNotificationController::class, 'getCount'])->name('count');
            Route::delete('/{id}', [SuperAdminNotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [SuperAdminNotificationController::class, 'clearAll'])->name('clear-all');
        });
    });

// ==========================================
// ADMIN ROUTES
// ==========================================

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::any('/', fn() => redirect()->route('admin.dashboard.index'))->name('index');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

        // Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'index'])->name('index');
            Route::put('/', [AdminProfileController::class, 'update'])->name('update');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/data', [AdminUserController::class, 'getData'])->name('data');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{uuid}', [AdminUserController::class, 'show'])->name('show');
            Route::get('/{uuid}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{uuid}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{uuid}', [AdminUserController::class, 'destroy'])->name('destroy');

            // AJAX endpoints
            Route::patch('/update-role', [AdminUserController::class, 'updateRole'])->name('update-role');
            Route::post('/{uuid}/unassign-rfid', [AdminUserController::class, 'unassignRfid'])->name('unassign-rfid');
            Route::get('/rfid/available', [AdminUserController::class, 'getAvailableRfidTags'])->name('rfid.available');
            Route::get('/stats', [AdminUserController::class, 'getStats'])->name('stats');
            Route::get('/{uuid}/coin-info', [AdminUserController::class, 'getCoinInfo'])->name('coin-info');
            Route::post('/{uuid}/sync-koin', [AdminUserController::class, 'syncKoin'])->name('sync-koin');
        });

        // RFID Tags Management
        Route::prefix('rfid-tags')->name('rfid-tags.')->group(function () {
            Route::get('/', [AdminRfidTagController::class, 'index'])->name('index');
            Route::get('/data', [AdminRfidTagController::class, 'getData'])->name('data');
            Route::post('/', [AdminRfidTagController::class, 'store'])->name('store');
            Route::get('/{rfidTag}', [AdminRfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [AdminRfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [AdminRfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [AdminRfidTagController::class, 'destroy'])->name('destroy');

            // AJAX endpoints
            Route::get('/stats', [AdminRfidTagController::class, 'getStats'])->name('stats');
            Route::get('/available', [AdminRfidTagController::class, 'getAvailable'])->name('available');
            Route::post('/release/{rfidTag}', [AdminRfidTagController::class, 'releaseFromUser'])->name('release');
            Route::post('/bulk-status', [AdminRfidTagController::class, 'bulkUpdateStatus'])->name('bulk-status');
        });

        // Items Management
        Route::prefix('items')->name('items.')->group(function () {
            // Basic CRUD
            Route::get('/', [AdminItemController::class, 'index'])->name('index');
            Route::get('/create', [AdminItemController::class, 'create'])->name('create');
            Route::post('/', [AdminItemController::class, 'store'])->name('store');
            Route::get('/{item}', [AdminItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [AdminItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [AdminItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [AdminItemController::class, 'destroy'])->name('destroy');

            // Soft Delete Management
            Route::post('/{id}/restore', [AdminItemController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [AdminItemController::class, 'forceDestroy'])->name('force-destroy');

            // Data endpoints
            Route::get('/data/items', [AdminItemController::class, 'getData'])->name('data');
            Route::get('/data/deleted', [AdminItemController::class, 'getDeletedData'])->name('deleted-data');

            // Status & Validation
            Route::post('/{item}/change-status', [AdminItemController::class, 'changeStatus'])->name('change-status');
            Route::get('/check-epc', [AdminItemController::class, 'checkEpc'])->name('check-epc');

            // Real-time endpoints
            Route::get('/check-updates', [AdminItemController::class, 'checkUpdates'])->name('check-updates');
            Route::get('/stats', [AdminItemController::class, 'getStats'])->name('stats');
            Route::post('/force-refresh', [AdminItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::post('/force-refresh-all', [AdminItemController::class, 'forceRefreshAll'])->name('force-refresh-all');
            Route::post('/warm-all-caches', [AdminItemController::class, 'warmAllCaches'])->name('warm-all-caches');
            Route::get('/cache-status', [AdminItemController::class, 'getCacheStatus'])->name('cache-status');
        });

        // Log Peminjaman
        Route::prefix('log_peminjaman')->name('log_peminjaman.')->group(function () {
            Route::get('/', [AdminLogPeminjamanController::class, 'index'])->name('index');
            Route::get('/data', [AdminLogPeminjamanController::class, 'getData'])->name('data');
            Route::get('/export', [AdminLogPeminjamanController::class, 'export'])->name('export');
            Route::post('/clear', [AdminLogPeminjamanController::class, 'clear'])->name('clear');
            Route::get('/dashboard', [AdminLogPeminjamanController::class, 'dashboard'])->name('dashboard');
            Route::get('/user/{userId}/history', [AdminLogPeminjamanController::class, 'userHistory'])->name('user.history');
            Route::get('/item/{itemId}/status', [AdminLogPeminjamanController::class, 'checkItemStatus'])->name('item.status');
        });

        // Missing Tools
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [AdminMissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [AdminMissingToolsController::class, 'getData'])->name('data');
            Route::get('/{id}', [AdminMissingToolsController::class, 'show'])->name('show');

            // Actions
            Route::post('/mark-missing/{itemId}', [AdminMissingToolsController::class, 'markAsMissing'])->name('mark-missing');
            Route::post('/{id}/reclaim', [AdminMissingToolsController::class, 'reclaimMissingItem'])->name('reclaim');
            Route::post('/{id}/cancel', [AdminMissingToolsController::class, 'cancelMissingTool'])->name('cancel');
            Route::get('/api/all', [AdminMissingToolsController::class, 'getAllMissingTools'])->name('api.all');
        });

        // Activity Logs
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/', [AdminActivityLogController::class, 'index'])->name('index');
            Route::get('/data', [AdminActivityLogController::class, 'getData'])->name('data');
            Route::post('/clear', [AdminActivityLogController::class, 'clear'])->name('clear');
            Route::get('/stats', [AdminActivityLogController::class, 'getStats'])->name('stats');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
            Route::get('/count', [AdminNotificationController::class, 'getCount'])->name('count');
            Route::delete('/{id}', [AdminNotificationController::class, 'destroy'])->name('destroy');
            Route::post('/clear-all', [AdminNotificationController::class, 'clearAll'])->name('clear-all');
        });
    });

// ==========================================
// API ROUTES (Optional - for better performance)
// ==========================================

Route::middleware(['auth:sanctum'])->prefix('api')->name('api.')->group(function () {

    // User API routes
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/items/check-updates', [UserItemController::class, 'checkUpdates'])->name('items.check-updates');
        Route::get('/items/stats', [UserItemController::class, 'getStats'])->name('items.stats');
        Route::post('/items/force-refresh', [UserItemController::class, 'forceRefresh'])->name('items.force-refresh');
    });

    // Admin API routes  
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/items/check-updates', [AdminItemController::class, 'checkUpdates'])->name('items.check-updates');
        Route::get('/items/stats', [AdminItemController::class, 'getStats'])->name('items.stats');
        Route::post('/items/force-refresh-all', [AdminItemController::class, 'forceRefreshAll'])->name('items.force-refresh-all');
    });

    // SuperAdmin API routes
    Route::middleware(['superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/items/check-updates', [SuperAdminItemController::class, 'checkUpdates'])->name('items.check-updates');
        Route::get('/items/stats', [SuperAdminItemController::class, 'getStats'])->name('items.stats');
        Route::post('/items/force-refresh-all', [SuperAdminItemController::class, 'forceRefreshAll'])->name('items.force-refresh-all');
        Route::get('/items/cache-status', [SuperAdminItemController::class, 'getCacheStatus'])->name('items.cache-status');
    });
});

// ==========================================
// DEVELOPMENT/DEBUG ROUTES (Only in debug mode)
// ==========================================

if (config('app.debug')) {
    Route::middleware(['auth'])->prefix('debug')->name('debug.')->group(function () {

        // Cache debugging routes
        Route::get('/cache/user-items', [UserItemController::class, 'getCacheInfo'])->name('cache.user-items');
        Route::get('/cache/admin-items', [SuperAdminItemController::class, 'getCacheStatus'])->name('cache.admin-items');

        // Performance testing routes
        Route::get('/performance/user-items', function () {
            $start = microtime(true);
            $controller = new App\Http\Controllers\User\ItemController();
            $stats = $controller->getCurrentStats();
            $end = microtime(true);

            return response()->json([
                'execution_time' => ($end - $start) * 1000 . ' ms',
                'stats' => $stats,
                'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB'
            ]);
        })->name('performance.user-items');
    });
}
