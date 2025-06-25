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
    Route::any('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    // Logout Route
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// PERBAIKAN: Tambahkan route untuk guest dashboard
Route::prefix('guest')->name('guest.')->group(function () {

    // Main dashboard page
    Route::get('/dashboard', [App\Http\Controllers\Guest\DashboardController::class, 'index'])->name('dashboard.index');

    // Tools data for DataTables AJAX
    Route::get('/tools/data', [App\Http\Controllers\Guest\DashboardController::class, 'getToolsData'])->name('tools.data');

    // Real-time update checking
    Route::get('/tools/check-updates', [App\Http\Controllers\Guest\DashboardController::class, 'checkUpdates'])->name('tools.check-updates');

    // Stats API
    Route::get('/tools/stats', [App\Http\Controllers\Guest\DashboardController::class, 'getStats'])->name('tools.stats');
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

        // Dashboard AJAX routes
        Route::get('/dashboard/refresh', [App\Http\Controllers\User\DashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::get('/dashboard/stats', [App\Http\Controllers\User\DashboardController::class, 'getStats'])->name('dashboard.stats');
        Route::get('/dashboard/activities', [App\Http\Controllers\User\DashboardController::class, 'getRecentActivitiesAjax'])->name('dashboard.activities');
        Route::get('/dashboard/chart-data', [App\Http\Controllers\User\DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
        Route::post('/dashboard/sync-koin', [App\Http\Controllers\User\DashboardController::class, 'syncKoin'])->name('dashboard.sync-koin');

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\ProfileController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\User\ProfileController::class, 'update'])->name('update');
        });
        // Storage routes
        Route::prefix('storage')->name('storage.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\StorageController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\User\StorageController::class, 'getData'])->name('data'); // New route for DataTables
            Route::get('/stats', [App\Http\Controllers\User\StorageController::class, 'getStats'])->name('stats');
            Route::get('/search', [App\Http\Controllers\User\StorageController::class, 'search'])->name('search');
            Route::get('/export', [App\Http\Controllers\User\StorageController::class, 'export'])->name('export');
            Route::get('/{id}', [App\Http\Controllers\User\StorageController::class, 'show'])->name('show');
            Route::patch('/{id}/return', [App\Http\Controllers\User\StorageController::class, 'returnItem'])->name('return');
        });
        Route::prefix('log-peminjaman')->name('log-peminjaman.')->group(function () {
            // Index page - menampilkan halaman utama log peminjaman user
            Route::get('/', [\App\Http\Controllers\User\LogPeminjamanController::class, 'index'])
                ->name('index');
            // Data endpoint - untuk DataTables AJAX
            Route::get('/data', [\App\Http\Controllers\User\LogPeminjamanController::class, 'getData'])
                ->name('data');
            // Stats endpoint - untuk mendapatkan statistik user
            Route::get('/stats', [\App\Http\Controllers\User\LogPeminjamanController::class, 'getStats'])
                ->name('stats');
            // Currently borrowed endpoint - untuk mendapatkan item yang sedang dipinjam
            Route::get('/currently-borrowed', [\App\Http\Controllers\User\LogPeminjamanController::class, 'getCurrentBorrowed'])
                ->name('currently_borrowed');
        });

        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\ItemController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\User\ItemController::class, 'getData'])->name('data');
            Route::get('/create', [App\Http\Controllers\User\ItemController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\User\ItemController::class, 'store'])->name('store');
            Route::get('/{item}', [App\Http\Controllers\User\ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [App\Http\Controllers\User\ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [App\Http\Controllers\User\ItemController::class, 'update'])->name('update');
            Route::delete('/{item}', [App\Http\Controllers\User\ItemController::class, 'destroy'])->name('destroy');
            Route::patch('/{item}/quantity', [App\Http\Controllers\User\ItemController::class, 'updateQuantity'])->name('update-quantity');
            // Real-time update checking route
            Route::get('/items/check-updates', [App\Http\Controllers\User\ItemController::class, 'checkUpdates'])->name('user.items.check-updates');

            // Stats API route
            Route::get('/items/stats', [App\Http\Controllers\User\ItemController::class, 'getStats'])->name('user.items.stats');
        });

        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            Route::get('/', [App\Http\Controllers\User\MissingToolsController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\User\MissingToolsController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\User\MissingToolsController::class, 'getStats'])->name('stats');
            Route::get('/search', [App\Http\Controllers\User\MissingToolsController::class, 'search'])->name('search');
        });
    });
// Super Admin routes
Route::prefix('superadmin')
    ->middleware(['auth', 'superadmin'])
    ->name('superadmin.')
    ->group(function () {
        Route::any('/', function () {
            return redirect()->route('superadmin.dashboard.index');
        })->name('index');
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
            ->name('dashboard.index');

        // User management routes
        Route::prefix('users')->name('users.')->group(function () {
            // Main user management
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/data', [UserController::class, 'getData'])->name('data');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            // AJAX endpoints
            Route::patch('/update-role', [UserController::class, 'updateRole'])->name('update-role');
            Route::post('/{uuid}/unassign-rfid', [UserController::class, 'unassignRfid'])->name('unassign-rfid');
            Route::get('/rfid/available', [UserController::class, 'getAvailableRfidTags'])->name('rfid.available');
            Route::get('/stats', [UserController::class, 'getStats'])->name('stats');
            Route::get('/{uuid}/coin-info', [UserController::class, 'getCoinInfo']);
            Route::post('/{uuid}/sync-koin', [UserController::class, 'syncKoin']);
            Route::get('/{uuid}', [UserController::class, 'show'])->name('show');
            Route::get('/{uuid}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{uuid}', [UserController::class, 'update'])->name('update');
            Route::delete('/{uuid}', [UserController::class, 'destroy'])->name('destroy');
        });
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
            Route::get('/', [RfidTagController::class, 'index'])->name('index');
            Route::get('/data', [RfidTagController::class, 'getData'])->name('data');
            Route::get('/stats', [RfidTagController::class, 'getStats'])->name('stats');
            Route::get('/available', [RfidTagController::class, 'getAvailable'])->name('available');
            Route::post('/', [RfidTagController::class, 'store'])->name('store');
            Route::post('/release/{rfidTag}', [RfidTagController::class, 'releaseFromUser'])->name('release');
            Route::post('/bulk-status', [RfidTagController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::get('/{rfidTag}', [RfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [RfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [RfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [RfidTagController::class, 'destroy'])->name('destroy');
            Route::get('/superadmin/users/available', [RfidTagController::class, 'getAvailableUsers']);
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\SuperAdmin\ProfileController::class, 'update'])->name('update');
        });

        // Items management routes
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/create', [ItemController::class, 'create'])->name('create');
            Route::post('/', [ItemController::class, 'store'])->name('store');


            // Soft delete (default delete route)
            Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');

            // NEW: Soft delete management routes
            Route::post('/{id}/restore', [ItemController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [ItemController::class, 'forceDestroy'])->name('force-destroy');

            // Data endpoints
            Route::get('/data/items', [ItemController::class, 'getData'])->name('data');
            Route::get('/data/deleted', [ItemController::class, 'getDeletedData'])->name('deleted-data');

            // Status and validation endpoints
            Route::post('/{item}/change-status', [ItemController::class, 'changeStatus'])->name('change-status');
            Route::get('/check-epc', [ItemController::class, 'checkEpc'])->name('check-epc');
            Route::get('/check-updates', [ItemController::class, 'checkUpdates'])->name('check-updates');
            Route::get('/check-status-updates', [ItemController::class, 'checkStatusUpdates'])->name('check-status-updates');

            // Real-time endpoints
            Route::post('/force-refresh', [ItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::get('/stats', [ItemController::class, 'getStats'])->name('stats');

            Route::get('/{item}', [ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [ItemController::class, 'update'])->name('update');
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
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            // Display missing tools page
            Route::get('/', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'index'])->name('index');
            // Get missing tools data for DataTables AJAX
            Route::get('/data', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'getData'])->name('data');

            // Mark item as missing (called from Items management)
            Route::post('/mark-missing/{itemId}', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'markAsMissing'])->name('mark-missing');
            // Reclaim missing tool (mark as completed)
            Route::post('/{id}/reclaim', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'reclaimMissingItem'])->name('reclaim');
            // Cancel missing tool report (mark as cancelled)
            Route::post('/{id}/cancel', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'cancelMissingTool'])->name('cancel');
            // Get all missing tools (API endpoint)
            Route::get('/api/all', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'getAllMissingTools'])->name('api.all');
            // Show missing tool details
            Route::get('/{id}', [App\Http\Controllers\SuperAdmin\MissingToolsController::class, 'show'])->name('show');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'index'])
                ->name('index');
            Route::get('/count', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'getCount'])
                ->name('count');
            // Ganti POST dengan DELETE
            Route::delete('/{id}', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'destroy'])
                ->name('destroy');
            // Ganti POST dengan DELETE  
            Route::post('/clear-all', [App\Http\Controllers\SuperAdmin\NotificationController::class, 'clearAll'])
                ->name('clear-all');
        });
    });

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {
        Route::any('/', function () {
            return redirect()->route('admin.dashboard.index');
        })->name('index');
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard.index');

        // User management routes
        Route::prefix('users')->name('users.')->group(function () {
            // Main user management
            Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\UserController::class, 'getData'])->name('data');
            Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
            // AJAX endpoints
            Route::patch('/update-role', [App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('update-role');
            Route::post('/{uuid}/unassign-rfid', [App\Http\Controllers\Admin\UserController::class, 'unassignRfid'])->name('unassign-rfid');
            Route::get('/rfid/available', [App\Http\Controllers\Admin\UserController::class, 'getAvailableRfidTags'])->name('rfid.available');
            Route::get('/stats', [App\Http\Controllers\Admin\UserController::class, 'getStats'])->name('stats');
            Route::get('/{uuid}/coin-info', [App\Http\Controllers\Admin\UserController::class, 'getCoinInfo']);
            Route::post('/{uuid}/sync-koin', [App\Http\Controllers\Admin\UserController::class, 'syncKoin']);
            Route::get('/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('show');
            Route::get('/{uuid}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
            Route::put('/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
            Route::delete('/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
        });
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
            Route::get('/', [App\Http\Controllers\Admin\RfidTagController::class, 'index'])->name('index');
            Route::get('/data', [App\Http\Controllers\Admin\RfidTagController::class, 'getData'])->name('data');
            Route::get('/stats', [App\Http\Controllers\Admin\RfidTagController::class, 'getStats'])->name('stats');
            Route::get('/available', [App\Http\Controllers\Admin\RfidTagController::class, 'getAvailable'])->name('available');
            Route::post('/', [App\Http\Controllers\Admin\RfidTagController::class, 'store'])->name('store');
            // PENTING: Route untuk mendapatkan daftar user yang tersedia
            Route::get('/available-users', [App\Http\Controllers\Admin\RfidTagController::class, 'getAvailableUsers'])->name('available-users');
            // Bulk operations
            Route::post('/bulk-status', [App\Http\Controllers\Admin\RfidTagController::class, 'bulkUpdateStatus'])->name('bulk-status');
            Route::post('/release/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'releaseFromUser'])->name('release');
            Route::get('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'show'])->name('show');
            Route::get('/{rfidTag}/edit', [App\Http\Controllers\Admin\RfidTagController::class, 'edit'])->name('edit');
            Route::put('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'update'])->name('update');
            Route::delete('/{rfidTag}', [App\Http\Controllers\Admin\RfidTagController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('index');
            Route::put('/', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
        });

        // Items management routes
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ItemController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Admin\ItemController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Admin\ItemController::class, 'store'])->name('store');


            // Soft delete (default delete route)
            Route::delete('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'destroy'])->name('destroy');

            // NEW: Soft delete management routes
            Route::post('/{id}/restore', [App\Http\Controllers\Admin\ItemController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [App\Http\Controllers\Admin\ItemController::class, 'forceDestroy'])->name('force-destroy');

            // Data endpoints
            Route::get('/data/items', [App\Http\Controllers\Admin\ItemController::class, 'getData'])->name('data');
            Route::get('/data/deleted', [App\Http\Controllers\Admin\ItemController::class, 'getDeletedData'])->name('deleted-data');

            // Status and validation endpoints
            Route::post('/{item}/change-status', [App\Http\Controllers\Admin\ItemController::class, 'changeStatus'])->name('change-status');
            Route::get('/check-epc', [App\Http\Controllers\Admin\ItemController::class, 'checkEpc'])->name('check-epc');
            Route::get('/check-updates', [App\Http\Controllers\Admin\ItemController::class, 'checkUpdates'])->name('check-updates');

            // Real-time endpoints
            Route::post('/force-refresh', [App\Http\Controllers\Admin\ItemController::class, 'forceRefresh'])->name('force-refresh');
            Route::get('/stats', [App\Http\Controllers\Admin\ItemController::class, 'getStats'])->name('stats');

            Route::get('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'show'])->name('show');
            Route::get('/{item}/edit', [App\Http\Controllers\Admin\ItemController::class, 'edit'])->name('edit');
            Route::put('/{item}', [App\Http\Controllers\Admin\ItemController::class, 'update'])->name('update');
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
        Route::prefix('missing-tools')->name('missing-tools.')->group(function () {
            // Display missing tools page
            Route::get('/', [App\Http\Controllers\Admin\MissingToolsController::class, 'index'])->name('index');
            // Get missing tools data for DataTables AJAX
            Route::get('/data', [App\Http\Controllers\Admin\MissingToolsController::class, 'getData'])->name('data');

            // Mark item as missing (called from Items management)
            Route::post('/mark-missing/{itemId}', [App\Http\Controllers\Admin\MissingToolsController::class, 'markAsMissing'])->name('mark-missing');
            // Reclaim missing tool (mark as completed)
            Route::post('/{id}/reclaim', [App\Http\Controllers\Admin\MissingToolsController::class, 'reclaimMissingItem'])->name('reclaim');
            // Cancel missing tool report (mark as cancelled)
            Route::post('/{id}/cancel', [App\Http\Controllers\Admin\MissingToolsController::class, 'cancelMissingTool'])->name('cancel');
            // Get all missing tools (API endpoint)
            Route::get('/api/all', [App\Http\Controllers\Admin\MissingToolsController::class, 'getAllMissingTools'])->name('api.all');
            // Show missing tool details
            Route::get('/{id}', [App\Http\Controllers\Admin\MissingToolsController::class, 'show'])->name('show');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\NotificationController::class, 'index'])
                ->name('index');
            Route::get('/count', [App\Http\Controllers\Admin\NotificationController::class, 'getCount'])
                ->name('count');
            // Ganti POST dengan DELETE
            Route::delete('/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])
                ->name('destroy');
            // Ganti POST dengan DELETE  
            Route::post('/clear-all', [App\Http\Controllers\Admin\NotificationController::class, 'clearAll'])
                ->name('clear-all');
        });
    });
