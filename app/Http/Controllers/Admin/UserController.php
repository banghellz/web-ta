<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'title' => 'User Management',
            'content' => 'Kelola semua user dalam sistem'
        ]);
    }

    public function getData()
    {
        $users = User::query();

        return DataTables::of($users)
            ->addIndexColumn() // Ini untuk nomor yang dimulai dari 1
            ->addColumn('name_link', function ($user) {
                // Return name as a clickable link
                return '<a href="javascript:void(0);" class="name-detail" data-id="' . $user->uuid . '">' . $user->name . '</a>';
            })
            ->addColumn('role_select', function ($user) {
                $roles = ['guest', 'user', 'admin'];
                $html = '<select data-user-id="' . $user->uuid . '" class="role-select border rounded p-1 w-full bg-white text-gray-700">';

                foreach ($roles as $role) {
                    $selected = $user->role === $role ? 'selected' : '';
                    $html .= '<option value="' . $role . '" ' . $selected . '>' . $role . '</option>';
                }

                $html .= '</select>';
                return $html;
            })
            ->addColumn('status_badge', function ($user) {
                if ($user->status == 'active') {
                    return '<span class="badge bg-success">Active</span>';
                } else {
                    return '<span class="badge bg-danger">Inactive</span>';
                }
            })
            ->addColumn('created_at_formatted', function ($user) {
                return $user->created_at->format('d-m-Y H:i');
            })
            ->addColumn('actions', function ($user) {
                $editBtn = '<a href="' . route('superadmin.users.edit', $user->uuid) . '" class="btn btn-sm btn-primary me-1">
                                <i class="ti ti-edit"></i> Edit
                            </a>';

                $deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $user->uuid . '" data-name="' . $user->name . '">
                                <i class="ti ti-trash"></i> Delete
                              </button>';

                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['name_link', 'role_select', 'status_badge', 'actions']) // Menandai kolom yang berisi HTML
            ->toJson();
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,uuid',
            'role' => 'required|in:guest,user,admin,superadmin'
        ]);

        $user = User::where('uuid', $request->user_id)->firstOrFail();
        $user->role = $request->role;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Show user details
     */
    public function show($uuid)
    {
        $user = User::with('detail')->where('uuid', $uuid)->firstOrFail();

        $html = View::make('admin.users.detail-partial', compact('user'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
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

        return view('admin.users.edit', [
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
            'pict' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'nim' => 'nullable|string|max:20',
            'no_koin' => 'nullable|string|max:50',
            'prodi' => 'nullable|string|max:100',
            'rfid_uid' => 'nullable|string|exists:rfid_tags,uid'
        ];

        $request->validate($validationRules);

        // Update user information
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        // Handle profile picture upload
        $pictPath = null;
        if ($request->hasFile('pict')) {
            // Delete old picture if exists
            if ($user->detail && $user->detail->pict) {
                Storage::disk('public')->delete('profile_pictures/' . $user->detail->pict);
            }

            $file = $request->file('pict');
            $fileName = time() . '_' . $user->uuid . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile_pictures'), $fileName);
            $pictPath = $fileName;
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

        // Only update picture if new one was uploaded
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

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User data has been updated successfully');
    }

    /**
     * Delete user
     */
    public function destroy($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Handle RFID tag release before deletion
        if ($user->detail && $user->detail->rfid_uid) {
            $rfidTag = RfidTag::where('uid', $user->detail->rfid_uid)->first();
            if ($rfidTag) {
                $rfidTag->markAsAvailable();
            }
        }

        // Delete profile picture if exists
        if ($user->detail && $user->detail->pict) {
            Storage::disk('public')->delete('profile_pictures/' . $user->detail->pict);
        }

        // Delete related details first if needed
        if ($user->detail) {
            $user->detail->delete();
        }

        $user->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get available RFID tags for AJAX requests
     */
    public function getAvailableRfidTags()
    {
        $rfidTags = RfidTag::where('status', 'Available')->get(['uid', 'notes']);

        return response()->json([
            'success' => true,
            'data' => $rfidTags
        ]);
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

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No RFID tag assigned']);
    }
}
