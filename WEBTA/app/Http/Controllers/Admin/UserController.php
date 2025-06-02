<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
                $roles = ['admin', 'user', 'super_admin'];
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
                $editBtn = '<a href="' . route('admin.users.edit', $user->uuid) . '" class="btn btn-sm btn-primary me-1">
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
            'role' => 'required|in:admin,user,super_admin'
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

        return view('admin.users.edit', [
            'title' => 'Edit User',
            'content' => 'Edit data user',
            'user' => $user
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
            'role' => 'required|in:admin,user,super_admin',
        ];

        // Validate student details if checkbox is checked
        if ($request->has('has_student_details')) {
            $validationRules['nim'] = 'nullable|string|max:20';
            $validationRules['no_koin'] = 'nullable|string|max:50';
            $validationRules['prodi'] = 'nullable|string|max:100';
        }

        $request->validate($validationRules);

        // Update user information
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        $user->save();

        // Handle student details
        if ($request->has('has_student_details')) {
            // If user already has details, update them
            if ($user->detail) {
                $user->detail->update([
                    'nim' => $request->nim,
                    'no_koin' => $request->no_koin,
                    'prodi' => $request->prodi,
                ]);
            } else {
                // Create new details if they don't exist
                UserDetail::create([
                    'user_id' => $user->id,
                    'nim' => $request->nim,
                    'no_koin' => $request->no_koin,
                    'prodi' => $request->prodi,
                ]);
            }
        } else if ($user->detail) {
            // Remove details if checkbox is unchecked but details exist
            $user->detail->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User data has been updated successfully');
    }

    /**
     * Delete user
     */
    public function destroy($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        // Delete related details first if needed
        if ($user->detail) {
            $user->detail->delete();
        }

        $user->delete();

        return response()->json(['success' => true]);
    }
}
