<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\RfidTag;
use App\Mail\AccountDeletionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        // Get user statistics
        $totalUsers = User::count();
        $adminUsers = User::whereIn('role', ['admin', 'superadmin'])->count();
        $guestUsers = User::where('role', 'guest')->count();

        return view('superadmin.users.index', [
            'title' => 'User Management',
            'content' => 'Kelola semua user dalam sistem',
            'totalUsers' => $totalUsers,
            'adminUsers' => $adminUsers,
            'guestUsers' => $guestUsers
        ]);
    }

    public function getData()
    {
        $users = User::query()->with('detail');
        $currentUserId = auth()->user()->uuid;

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('name_link', function ($user) {

                return '<div class="d-flex align-items-center">' .
                    '<div>' .
                    '<a href="javascript:void(0);" class="name-detail text-decoration-none fw-medium" data-id="' . $user->uuid . '">' . $user->name . '</a>' .
                    ($user->detail && $user->detail->nim ? '<div class="text-muted small">NIM: ' . $user->detail->nim . '</div>' : '') .
                    '</div>' .
                    '</div>';
            })
            ->addColumn('role_select', function ($user) use ($currentUserId) {
                $roles = ['guest', 'user', 'admin', 'superadmin'];
                $roleColors = [
                    'guest' => 'secondary',
                    'user' => 'primary',
                    'admin' => 'warning',
                    'superadmin' => 'danger'
                ];

                // If this is the current user, disable the select
                $isCurrentUser = $user->uuid === $currentUserId;
                $disabled = $isCurrentUser ? 'disabled' : '';
                $title = $isCurrentUser ? 'title="You cannot change your own role"' : '';

                $html = '<select data-user-id="' . $user->uuid . '" data-original-role="' . $user->role . '" class="role-select form-select form-select-sm" ' . $disabled . ' ' . $title . '>';

                foreach ($roles as $role) {
                    $selected = $user->role === $role ? 'selected' : '';
                    $html .= '<option value="' . $role . '" ' . $selected . '>' . ucfirst($role) . '</option>';
                }

                $html .= '</select>';
                return $html;
            })
            ->addColumn('rfid_status', function ($user) {
                if ($user->detail && $user->detail->rfid_uid) {
                    return '<span class="badge bg-info"><i class="ti ti-credit-card me-1"></i>Assigned</span>';
                } else {
                    return '<span class="badge bg-secondary"><i class="ti ti-credit-card-off me-1"></i>No RFID</span>';
                }
            })
            ->addColumn('created_at_formatted', function ($user) {
                return '<div class="text-muted">' . $user->created_at->format('d M Y') . '</div><div class="text-muted small">' . $user->created_at->format('H:i') . '</div>';
            })
            ->addColumn('actions', function ($user) use ($currentUserId) {
                $editUrl = route('superadmin.users.edit', $user->uuid);
                $isCurrentUser = $user->uuid === $currentUserId;

                $actions = '
        <div class="d-flex justify-content-center align-items-center">
            <div class="dropdown">
                <button class="btn btn-actions" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions">
                    <li>
                        <a class="dropdown-item" href="' . $editUrl . '">
                            <i class="ti ti-edit me-2"></i>Edit
                        </a>
                    </li>';

                // Add unassign RFID option if user has RFID
                if ($user->detail && $user->detail->rfid_uid) {
                    $actions .= '
                    <li>
                        <a class="dropdown-item text-warning unassign-rfid" href="javascript:void(0);" 
                           data-id="' . $user->uuid . '" 
                           data-name="' . e($user->name) . '">
                            <i class="ti ti-credit-card-off me-2"></i>Unassign RFID
                        </a>
                    </li>';
                }

                // Add divider before delete only if not current user
                if (!$isCurrentUser) {
                    $actions .= '
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger btn-delete" href="javascript:void(0);" 
                           data-id="' . $user->uuid . '" 
                           data-name="' . e($user->name) . '" 
                           data-email="' . e($user->email) . '">
                            <i class="ti ti-trash me-2"></i>Delete
                        </a>
                    </li>';
                } else {
                    $actions .= '
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <span class="dropdown-item text-muted" title="You cannot delete your own account">
                            <i class="ti ti-trash me-2"></i>Delete (Not allowed)
                        </span>
                    </li>';
                }

                $actions .= '
                </ul>
            </div>
        </div>';

                return $actions;
            })
            ->with([
                'stats' => [
                    'total_users' => User::count(),
                    'admin_users' => User::whereIn('role', ['admin', 'superadmin'])->count(),
                    'guest_users' => User::where('role', 'guest')->count(),
                ]
            ])
            ->rawColumns(['name_link', 'role_select', 'rfid_status', 'created_at_formatted', 'actions'])
            ->toJson();
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,uuid',
            'role' => 'required|in:guest,user,admin,superadmin'
        ]);

        $user = User::where('uuid', $request->user_id)->firstOrFail();

        // Prevent user from changing their own role
        if ($user->uuid === auth()->user()->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role'
            ], 403);
        }

        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "User role updated from {$oldRole} to {$request->role}"
        ]);
    }

    /**
     * Show user details
     */
    /**
     * Show user details dengan sync koin
     */
    public function show($uuid)
    {
        try {
            $user = User::with('detail.borrowedItems')->where('uuid', $uuid)->firstOrFail();

            // Sync koin jika user bukan admin dan memiliki detail
            if ($user->detail) {
                $adminRoles = [
                    'admin',
                    'superadmin',
                    'super_admin',
                    'Admin',
                    'SuperAdmin',
                    'Super_Admin',
                    'ADMIN',
                    'SUPERADMIN',
                    'SUPER_ADMIN'
                ];

                $isAdminUser = in_array(trim($user->role ?? ''), $adminRoles, true);

                if (!$isAdminUser) {
                    $oldKoin = $user->detail->koin;
                    $user->detail->syncKoin();

                    Log::info('Synced coin for user in detail view', [
                        'user_id' => $user->id,
                        'uuid' => $user->uuid,
                        'old_koin' => $oldKoin,
                        'new_koin' => $user->detail->fresh()->koin,
                        'viewed_by_admin' => auth()->id()
                    ]);
                }
            }

            $html = View::make('superadmin.users.detail-partial', compact('user'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'user_data' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'role' => $user->role,
                    'has_detail' => $user->detail ? true : false,
                    'current_koin' => $user->detail ? $user->detail->koin : null,
                    'borrowed_count' => $user->detail ? $user->detail->borrowedItems()->count() : 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading user details: ' . $e->getMessage(), [
                'uuid' => $uuid
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load user details.'
            ], 500);
        }
    }
    /**
     * Show edit form
     */
    public function edit($uuid)
    {
        $user = User::with('detail')->where('uuid', $uuid)->firstOrFail();

        // Get available RFID tags and the current user's RFID tag
        $availableRfidTags = RfidTag::where('status', 'Available')->get();
        $currentRfidTag = null;

        if ($user->detail && $user->detail->rfid_uid) {
            $currentRfidTag = RfidTag::where('uid', $user->detail->rfid_uid)->first();
            // If current RFID tag exists, add it to available options
            if ($currentRfidTag) {
                $availableRfidTags->push($currentRfidTag);
            }
        }

        return view('superadmin.users.edit', [
            'title' => 'Edit User',
            'content' => 'Edit data user',
            'user' => $user,
            'availableRfidTags' => $availableRfidTags
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, $uuid)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($uuid, 'uuid')
            ],
            'role' => 'required|in:admin,user,superadmin,guest',
            'nim' => 'nullable|string|max:20',
            'no_koin' => 'nullable|string|max:50',
            'prodi' => 'nullable|string|max:100',
            'rfid_uid' => 'nullable|string|exists:rfid_tags,uid',
            'pict' => 'nullable|image|mimes:jpeg,png,jpg|max:10240' // Max 10MB
        ];

        try {
            $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Get user
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Prevent user from changing their own role
        if ($user->uuid === auth()->user()->uuid && $request->role !== $user->role) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own role'
                ], 403);
            }
            return redirect()->back()
                ->withErrors(['role' => 'You cannot change your own role'])
                ->withInput();
        }

        // Update user information
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        // Handle profile picture upload
        $pictPath = null;
        if ($request->hasFile('pict')) {
            try {
                // Delete old picture if exists
                if ($user->detail && $user->detail->pict) {
                    $oldPictPath = public_path('profile_pictures/' . $user->detail->pict);
                    if (file_exists($oldPictPath)) {
                        unlink($oldPictPath);
                    }
                }

                // Store new picture
                $file = $request->file('pict');
                $fileName = $user->uuid . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Ensure directory exists
                $uploadPath = public_path('profile_pictures');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $fileName);
                $pictPath = $fileName;
            } catch (\Exception $e) {
                Log::error('Profile picture upload failed: ' . $e->getMessage());
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to upload profile picture'
                    ], 500);
                }
                return redirect()->back()
                    ->withErrors(['pict' => 'Failed to upload profile picture'])
                    ->withInput();
            }
        }

        // Handle RFID tag assignment
        $oldRfidUid = null;
        if ($user->detail) {
            $oldRfidUid = $user->detail->rfid_uid;
        }

        // If RFID tag is being changed
        if ($request->rfid_uid !== $oldRfidUid) {
            // Mark old RFID tag as available if it exists
            if ($oldRfidUid) {
                $oldRfidTag = RfidTag::where('uid', $oldRfidUid)->first();
                if ($oldRfidTag) {
                    $oldRfidTag->markAsAvailable();
                }
            }

            // Mark new RFID tag as used if it's selected
            if ($request->rfid_uid) {
                $newRfidTag = RfidTag::where('uid', $request->rfid_uid)->first();
                if ($newRfidTag && $newRfidTag->status === 'Available') {
                    $newRfidTag->markAsUsed();
                }
            }
        }

        // Handle student details
        $detailData = [
            'nim' => $request->nim,
            'no_koin' => $request->no_koin,
            'prodi' => $request->prodi,
            'rfid_uid' => $request->rfid_uid,
        ];

        // Add picture path if uploaded
        if ($pictPath) {
            $detailData['pict'] = $pictPath;
        }

        // Update or create user details
        if ($user->detail) {
            $user->detail->update($detailData);
        } else {
            $detailData['user_id'] = $user->id;
            UserDetail::create($detailData);
        }

        // Check if it's an AJAX request
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User data has been updated successfully',
                'redirect' => route('superadmin.users.index')
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User data has been updated successfully');
    }

    /**
     * Delete user with email notification
     */
    public function destroy(Request $request, $uuid)
    {
        $request->validate([
            'deletion_reason' => 'nullable|string|max:500'
        ]);

        $user = User::where('uuid', $uuid)->firstOrFail();

        // Prevent user from deleting their own account
        if ($user->uuid === auth()->user()->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 403);
        }

        $userEmail = $user->email;
        $userName = $user->name;
        $deletionReason = $request->deletion_reason;

        try {
            DB::beginTransaction();

            // Handle RFID tag release before deletion
            if ($user->detail && $user->detail->rfid_uid) {
                $rfidTag = RfidTag::where('uid', $user->detail->rfid_uid)->first();
                if ($rfidTag) {
                    $rfidTag->markAsAvailable();
                }
            }

            // Store user data for email before deletion
            $userForEmail = $user->load('detail');

            // Delete related details first if needed
            if ($user->detail) {
                $user->detail->delete();
            }

            $user->delete();

            // Send deletion notification email
            try {
                Mail::to($userEmail)->send(new AccountDeletionNotification($userForEmail, $deletionReason));
            } catch (\Exception $mailException) {
                // Log email error but don't fail the deletion
                Log::warning('Failed to send account deletion email to ' . $userEmail . ': ' . $mailException->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "User '{$userName}' has been deleted successfully. Deletion notification has been sent to {$userEmail}."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user. Please try again.'
            ], 500);
        }
    }

    public function unassignRfid($uuid)
    {
        $user = User::with('detail')->where('uuid', $uuid)->firstOrFail();

        if ($user->detail && $user->detail->rfid_uid) {
            // Mark RFID tag as available
            $rfidTag = RfidTag::where('uid', $user->detail->rfid_uid)->first();
            if ($rfidTag) {
                $rfidTag->markAsAvailable();
            }

            // Remove RFID from user detail
            $user->detail->update(['rfid_uid' => null]);

            return response()->json([
                'success' => true,
                'message' => 'RFID tag has been unassigned successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No RFID tag assigned to this user'
        ], 400);
    }
    /**
     * Get fresh coin information for a user
     * 
     * @param string $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCoinInfo($uuid)
    {
        try {
            // FIX: Gunakan where('uuid') bukan find()
            $user = User::with('detail.borrowedItems')->where('uuid', $uuid)->firstOrFail();

            $userDetail = $user->detail;

            if (!$userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User detail not found'
                ], 404);
            }

            // Check if user is admin
            $adminRoles = [
                'admin',
                'superadmin',
                'super_admin',
                'Admin',
                'SuperAdmin',
                'Super_Admin',
                'ADMIN',
                'SUPERADMIN',
                'SUPER_ADMIN'
            ];

            $isAdmin = in_array(trim($user->role ?? ''), $adminRoles, true);
            $borrowedCount = $userDetail->borrowedItems()->count();

            return response()->json([
                'success' => true,
                'message' => 'Coin information retrieved successfully',
                'user_detail' => $userDetail,
                'is_admin' => $isAdmin,
                'borrowed_count' => $borrowedCount,
                'calculation_info' => [
                    'base_koin' => 10,
                    'borrowed_items' => $borrowedCount,
                    'current_koin' => $userDetail->koin,
                    'formula' => "10 - {$borrowedCount} = {$userDetail->koin}"
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get coin info: ' . $e->getMessage(), [
                'uuid' => $uuid
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve coin information'
            ], 500);
        }
    }

    /**
     * Sync user koin berdasarkan jumlah item yang dipinjam
     * 
     * @param string $uuid (BUKAN $userId)
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncKoin($uuid)
    {
        try {
            // FIX: Gunakan where('uuid') bukan find(), parameter juga $uuid bukan $userId
            $user = User::with('detail.borrowedItems')->where('uuid', $uuid)->firstOrFail();

            $userDetail = $user->detail;

            if (!$userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'User detail not found'
                ], 404);
            }

            // Check if user is admin
            $adminRoles = [
                'admin',
                'superadmin',
                'super_admin',
                'Admin',
                'SuperAdmin',
                'Super_Admin',
                'ADMIN',
                'SUPERADMIN',
                'SUPER_ADMIN'
            ];

            $isAdmin = in_array(trim($user->role ?? ''), $adminRoles, true);

            if ($isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin users do not need coin synchronization',
                    'is_admin' => true
                ], 400);
            }

            // Simpan koin lama untuk perbandingan
            $oldKoin = $userDetail->koin;

            // Hitung jumlah item yang sedang dipinjam
            $borrowedCount = $userDetail->borrowedItems()->count();

            // Panggil fungsi syncKoin dari UserDetail model
            $userDetail->syncKoin();

            // Ambil data fresh setelah sync
            $userDetail->refresh();

            Log::info('User koin synchronized by admin', [
                'user_id' => $user->id, // Database ID untuk internal
                'user_uuid' => $user->uuid, // UUID untuk tracking
                'user_name' => $user->name,
                'old_koin' => $oldKoin,
                'new_koin' => $userDetail->koin,
                'borrowed_count' => $borrowedCount,
                'calculation' => "10 - {$borrowedCount} = {$userDetail->koin}",
                'synced_by_admin' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Coins synchronized successfully. {$oldKoin} → {$userDetail->koin} coins",
                'user_detail' => $userDetail,
                'is_admin' => false,
                'old_koin' => $oldKoin,
                'borrowed_count' => $borrowedCount,
                'calculation_info' => [
                    'base_koin' => 10,
                    'borrowed_items' => $borrowedCount,
                    'result' => $userDetail->koin,
                    'formula' => "10 - {$borrowedCount} = {$userDetail->koin}"
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync user koin by admin: ' . $e->getMessage(), [
                'uuid' => $uuid, // Log UUID yang diterima
                'admin_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while synchronizing coins: ' . $e->getMessage()
            ], 500);
        }
    }
}
