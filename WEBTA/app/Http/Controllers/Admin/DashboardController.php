<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Mendapatkan total user
        $userCount = User::count();

        // Mendapatkan total user aktif dalam 7 hari terakhir
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(7))->count();

        // Menghitung persentase user aktif
        $userCountPercentage = $userCount > 0 ?
            round(($activeUsers / $userCount) * 100) . '%' : '0%';

        return view('admin.dashboard.index', [
            'title' => 'Admin Dashboard',
            'content' => 'Management dashboard overview',
            'userCount' => $userCount,
            'activeUsers' => $activeUsers,
            'userCountPercentage' => $userCountPercentage,
        ]);
    }
}
