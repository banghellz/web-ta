<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompleteProfileController;
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

// Contoh route yang membutuhkan autentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Route untuk admin
// In your web.php routes file
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
        Route::get('/users/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{uuid}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{uuid}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/data', [App\Http\Controllers\Admin\ActivityLogController::class, 'getData'])->name('activity-logs.data');
        Route::post('/activity-logs/clear', [App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('activity-logs.clear');
        Route::get('/activity-logs/stats', [App\Http\Controllers\Admin\ActivityLogController::class, 'getStats'])->name('activity-logs.stats');
        // Route lainnya bisa ditambahkan di sini
        // Route::get('/settings', ...)->name('settings');
    });
// Route untuk user
Route::prefix('user')->middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});

Route::get('/complete-profile', [CompleteProfileController::class, 'index'])->name('user.complete-profile');
Route::post('/complete-profile', [CompleteProfileController::class, 'store'])->name('user.complete-profile.store');
