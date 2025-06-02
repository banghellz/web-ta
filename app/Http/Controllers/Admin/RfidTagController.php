<?php

namespace App\Http\Controllers\Admin;

use App\Models\RfidTag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class RfidTagController extends Controller
{
    /**
     * Display a listing of the RFID tags.
     */
    public function index()
    {
        return view('admin.rfid.index', [
            'title' => 'RFID Tags Management',
            'content' => 'Manage RFID tags for the system'
        ]);
    }

    /**
     * Get RFID tags data for DataTables
     */
    public function getData(Request $request)
    {
        $rfidTags = RfidTag::with('userDetail')->select('rfid_tags.*');

        return DataTables::of($rfidTags)
            ->addIndexColumn()
            ->addColumn('status', function ($tag) {
                $statusClass = [
                    'Available' => 'success',
                    'Used' => 'warning',
                    'Damaged' => 'danger'
                ];

                $class = $statusClass[$tag->status] ?? 'secondary';
                return '<span class="badge bg-' . $class . '">' . $tag->status . '</span>';
            })
            ->addColumn('assigned_to', function ($tag) {
                if ($tag->userDetail && $tag->userDetail->user) {
                    return $tag->userDetail->user->name;
                }
                return '<span class="text-muted">Not assigned</span>';
            })
            ->addColumn('created_at', function ($tag) {
                return $tag->created_at->format('d M Y, H:i');
            })
            ->addColumn('actions', function ($tag) {
                $editBtn = '<button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="' . $tag->id . '">
                    <i class="ti ti-edit"></i> Edit
                </button>';

                $deleteBtn = '<button class="btn btn-sm btn-outline-danger btn-delete" data-id="' . $tag->id . '" data-tag="' . $tag->uid . '">
                    <i class="ti ti-trash"></i> Delete
                </button>';

                return $editBtn . $deleteBtn;
            })
            ->rawColumns(['status', 'assigned_to', 'actions'])
            ->make(true);
    }

    /**
     * Get RFID tags statistics
     */
    public function getStats()
    {
        $stats = [
            'available' => RfidTag::where('status', 'Available')->count(),
            'used' => RfidTag::where('status', 'Used')->count(),
            'damaged' => RfidTag::where('status', 'Damaged')->count(),
            'total' => RfidTag::count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Show the specified RFID tag.
     */
    public function show(RfidTag $rfidTag)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rfidTag->id,
                'tag_id' => $rfidTag->uid,
                'name' => $rfidTag->notes,
                'is_active' => $rfidTag->status === 'Available'
            ]
        ]);
    }

    /**
     * Show the form for editing the specified RFID tag.
     */
    public function edit(RfidTag $rfidTag)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rfidTag->id,
                'tag_id' => $rfidTag->uid,
                'name' => $rfidTag->notes,
                'is_active' => $rfidTag->status === 'Available'
            ]
        ]);
    }

    /**
     * Store a newly created RFID tag in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'tag_id' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:rfid_tags,uid'
                ],
                'name' => 'nullable|string|max:255',
                'is_active' => 'boolean',
            ]);

            // Create the RFID tag
            $rfidTag = RfidTag::create([
                'uid' => $validated['tag_id'],
                'status' => ($validated['is_active'] ?? true) ? 'Available' : 'Damaged',
                'notes' => $validated['name'] ?? null
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
            // Validate the request
            $validated = $request->validate([
                'tag_id' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('rfid_tags', 'uid')->ignore($rfidTag->id),
                ],
                'name' => 'nullable|string|max:255',
                'is_active' => 'boolean',
            ]);

            // Update the RFID tag
            $rfidTag->update([
                'uid' => $validated['tag_id'],
                'status' => ($validated['is_active'] ?? true) ? 'Available' : 'Damaged',
                'notes' => $validated['name'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RFID tag updated successfully.',
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
                    'message' => 'Cannot delete RFID tag that is in use by a user.'
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
     * Bulk update RFID tag status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:rfid_tags,id',
            'status' => 'required|in:Available,Used,Damaged'
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
