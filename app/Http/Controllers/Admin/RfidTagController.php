<?php

namespace App\Http\Controllers\Admin;

use App\Models\RfidTag;
use App\Models\User;
use App\Models\UserDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class RfidTagController extends Controller
{
    /**
     * Display a listing of the RFID tags.
     */
    public function index()
    {
        $stats = $this->getStatistics();

        return view('admin.rfid.index', [
            'title' => 'RFID Tags Management',
            'content' => 'Manage RFID tags for the system',
            'totalTags' => $stats['total'],
            'availableTags' => $stats['available'],
            'usedTags' => $stats['used']
        ]);
    }

    /**
     * Get RFID tags data for DataTables
     */
    public function getData(Request $request)
    {
        $rfidTags = RfidTag::with(['userDetail.user'])->select('rfid_tags.*');

        return DataTables::of($rfidTags)
            ->addIndexColumn()
            ->addColumn('rfid_uid', function ($tag) {
                return '<div class="d-flex align-items-center">' .
                    '<span class="avatar avatar-sm me-2 bg-secondary text-white">' .
                    '<i class="ti ti-nfc"></i>' .
                    '</span>' .
                    '<div><strong>' . $tag->uid . '</strong></div>' .
                    '</div>';
            })
            ->addColumn('status', function ($tag) {
                $iconClass = '';
                $badgeClass = '';
                $statusText = '';

                if ($tag->status === 'Available') {
                    $iconClass = 'ti-check';
                    $badgeClass = 'bg-success';
                    $statusText = 'Available';
                } else if ($tag->status === 'Used') {
                    $iconClass = 'ti-user';
                    $badgeClass = 'bg-warning';
                    $statusText = 'Used';
                } else {
                    $iconClass = 'ti-help';
                    $badgeClass = 'bg-secondary';
                    $statusText = 'Unknown';
                }

                return '<span class="badge ' . $badgeClass . '"><i class="ti ' .
                    $iconClass . ' me-1"></i>' . $statusText . '</span>';
            })
            ->addColumn('assigned_to', function ($tag) {
                if ($tag->userDetail && $tag->userDetail->user) {
                    return '<div class="d-flex align-items-center">' .
                        '<span class="avatar avatar-sm me-2 bg-primary text-white">' .
                        '<i class="ti ti-user"></i>' .
                        '</span>' .
                        '<div>' .
                        '<div class="font-weight-medium">' . $tag->userDetail->user->name . '</div>' .
                        '<div class="text-muted small">' . ($tag->userDetail->nim ?? 'No NIM') . '</div>' .
                        '</div>' .
                        '</div>';
                }
                return '<span class="text-muted">Not assigned</span>';
            })
            ->addColumn('created_at_formatted', function ($tag) {
                return '<div class="text-muted">' . $tag->created_at->format('d M Y, H:i') . '</div>';
            })
            ->addColumn('actions', function ($tag) {
                $actions = '
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-actions" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-actions">
                                <li>
                                    <a class="dropdown-item btn-edit" href="#" data-id="' . $tag->id . '">
                                        <i class="ti ti-edit me-2"></i>Edit
                                    </a>
                                </li>';

                // Add release option for used RFID tags
                if ($tag->status === 'Used' && $tag->userDetail) {
                    $actions .= '
                                <li>
                                    <a class="dropdown-item text-warning release-rfid" href="#" 
                                       data-rfid-id="' . $tag->id . '" 
                                       data-rfid-uid="' . $tag->uid . '">
                                        <i class="ti ti-user-minus me-2"></i>Release from User
                                    </a>
                                </li>';
                }

                $actions .= '
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger delete-rfid" href="#" 
                                       data-rfid-id="' . $tag->id . '" 
                                       data-rfid-uid="' . $tag->uid . '">
                                        <i class="ti ti-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                ';

                return $actions;
            })
            ->rawColumns(['rfid_uid', 'status', 'assigned_to', 'created_at_formatted', 'actions'])
            ->with('stats', $this->getStatistics())
            ->make(true);
    }

    /**
     * Get RFID tags statistics
     */
    public function getStats()
    {
        $stats = $this->getStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get statistics data
     */
    private function getStatistics()
    {
        return [
            'available' => RfidTag::where('status', 'Available')->count(),
            'used' => RfidTag::where('status', 'Used')->count(),
            'total' => RfidTag::count()
        ];
    }

    /**
     * Show the form for editing the specified RFID tag.
     */
    public function edit(RfidTag $rfidTag)
    {
        $rfidTag->load(['userDetail.user']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rfidTag->id,
                'name' => $rfidTag->notes,
                'assigned_user_id' => $rfidTag->userDetail ? $rfidTag->userDetail->user_id : null
            ]
        ]);
    }

    /**
     * Store a newly created RFID tag in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'uid' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:rfid_tags,uid'
                ],
                'status' => 'required|string|in:Available,Used',
                'notes' => 'nullable|string|max:255',
            ]);

            $rfidTag = RfidTag::create([
                'uid' => $validated['uid'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RFID tag created successfully.',
                'data' => $rfidTag
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create RFID tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified RFID tag in storage.
     */
    public function update(Request $request, RfidTag $rfidTag)
    {
        try {
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'assigned_user_id' => 'nullable|exists:users,id',
            ]);

            // Debug log (opsional, bisa dihapus di production)
            Log::info('RFID Update started', [
                'rfid_id' => $rfidTag->id,
                'rfid_uid' => $rfidTag->uid,
                'assigned_user_id' => $validated['assigned_user_id'] ?? null
            ]);

            // Update RFID tag notes/name
            $rfidTag->update([
                'notes' => $validated['name'] ?? null
            ]);

            // Handle user assignment
            if (isset($validated['assigned_user_id'])) {
                if ($validated['assigned_user_id']) {
                    // Assign to user
                    $user = User::find($validated['assigned_user_id']);

                    if ($user && $user->detail) {
                        // Remove RFID from current user if assigned to someone else
                        UserDetail::where('rfid_uid', $rfidTag->uid)->update(['rfid_uid' => null]);

                        // Assign to new user
                        $user->detail->update(['rfid_uid' => $rfidTag->uid]);

                        // Mark RFID as used
                        $rfidTag->markAsUsed();

                        Log::info('RFID assigned successfully', [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'rfid_uid' => $rfidTag->uid
                        ]);
                    } else {
                        Log::error('User does not have detail', ['user_id' => $validated['assigned_user_id']]);
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected user does not have user details.'
                        ], 400);
                    }
                } else {
                    // Unassign from user
                    UserDetail::where('rfid_uid', $rfidTag->uid)->update(['rfid_uid' => null]);
                    $rfidTag->markAsAvailable();

                    Log::info('RFID unassigned successfully', ['rfid_uid' => $rfidTag->uid]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'RFID tag updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('RFID Update failed', [
                'error' => $e->getMessage(),
                'rfid_id' => $rfidTag->id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update RFID tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified RFID tag from storage.
     */
    public function destroy(RfidTag $rfidTag)
    {
        try {
            // Check if RFID tag is in use before deleting
            if ($rfidTag->userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete RFID tag that is assigned to a user.'
                ], 400);
            }

            $rfidTag->delete();

            return response()->json([
                'success' => true,
                'message' => 'RFID tag deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete RFID tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Release RFID tag from user
     */
    public function releaseFromUser(Request $request, RfidTag $rfidTag)
    {
        try {
            if (!$rfidTag->userDetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'RFID tag is not assigned to any user.'
                ], 400);
            }

            // Remove RFID from user
            $userDetail = $rfidTag->userDetail;
            $userDetail->update(['rfid_uid' => null]);

            // Mark RFID as available
            $rfidTag->markAsAvailable();

            return response()->json([
                'success' => true,
                'message' => 'RFID tag released from user successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to release RFID tag: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available RFID tags for selection
     */
    public function getAvailable()
    {
        $availableTags = RfidTag::where('status', 'Available')->get();

        return response()->json([
            'success' => true,
            'data' => $availableTags
        ]);
    }

    /**
     * Get available users for assignment - UPDATED TO SHOW DIFFERENT USERS FOR EDIT
     */
    public function getAvailableUsers(Request $request)
    {
        try {
            $rfidId = $request->get('rfid_id'); // Get RFID ID if provided

            // Base query: users with details
            $query = User::with('detail')->whereHas('detail');

            if ($rfidId) {
                // For edit mode: show users without RFID + current assigned user
                $currentRfid = RfidTag::find($rfidId);
                $currentUserId = $currentRfid && $currentRfid->userDetail ? $currentRfid->userDetail->user_id : null;

                if ($currentUserId) {
                    // Show users without RFID + current assigned user
                    $query->where(function ($q) use ($currentUserId) {
                        $q->whereHas('detail', function ($subQ) {
                            $subQ->whereNull('rfid_uid');
                        })->orWhere('id', $currentUserId);
                    });
                } else {
                    // Show only users without RFID
                    $query->whereHas('detail', function ($subQ) {
                        $subQ->whereNull('rfid_uid');
                    });
                }
            } else {
                // For create mode: show all users with indication of RFID status
                // No filtering needed for create mode
            }

            $users = $query->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nim' => $user->detail->nim ?? 'No NIM',
                    'email' => $user->email ?? '',
                    'has_rfid' => !empty($user->detail->rfid_uid),
                    'current_rfid' => $user->detail->rfid_uid ?? null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users loaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Bulk update RFID tag status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:rfid_tags,id',
            'status' => 'required|in:Available,Used'
        ]);

        try {
            RfidTag::whereIn('id', $request->tag_ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'RFID tags status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update RFID tags status: ' . $e->getMessage()
            ], 500);
        }
    }
}
