<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'user_id',
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user that caused this notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human readable time
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Create notification helper for superadmins
     */
    private static function createNotificationForSuperadmins($data)
    {
        $superadmins = User::where('role', 'superadmin')->get();

        foreach ($superadmins as $admin) {
            self::create([
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'data' => $data['data'] ?? [],
                'user_id' => $data['user_id'] ?? null,
            ]);
        }
    }

    /**
     * 1. New User Registration Notification
     */
    public static function userRegistered($user)
    {
        self::createNotificationForSuperadmins([
            'type' => 'user_registered',
            'title' => 'New User Registered',
            'message' => "{$user->name} has joined the system",
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
            ],
            'user_id' => $user->id,
        ]);
    }

    /**
     * 2. New Tool Added Notification
     */
    public static function toolAdded($item, $user = null)
    {
        self::createNotificationForSuperadmins([
            'type' => 'tool_added',
            'title' => 'New Tool Added',
            'message' => "'{$item->nama_barang}' added to inventory" . ($user ? " by {$user->name}" : ''),
            'data' => [
                'item_id' => $item->id,
                'item_name' => $item->nama_barang,
                'item_epc' => $item->epc,
                'added_by' => $user?->name,
            ],
            'user_id' => $user?->id,
        ]);
    }

    public static function toolEdited($item, $user = null)
    {
        self::createNotificationForSuperadmins([
            'type' => 'tool_edited',
            'title' => 'Tool Edited',
            'message' => "Successfuly edited '{$item->nama_barang}'" . ($user ? " by {$user->name}" : ''),
            'data' => [
                'item_id' => $item->id,
                'item_name' => $item->nama_barang,
                'item_epc' => $item->epc,
                'added_by' => $user?->name,
            ],
            'user_id' => $user?->id,
        ]);
    }

    /**
     * 3. Tool Deleted Notification
     */
    public static function toolDeleted($item, $user = null)
    {
        self::createNotificationForSuperadmins([
            'type' => 'tool_deleted',
            'title' => 'Tool Deleted',
            'message' => "'{$item->nama_barang}' removed from inventory" . ($user ? " by {$user->name}" : ''),
            'data' => [
                'item_id' => $item->id,
                'item_name' => $item->nama_barang,
                'item_epc' => $item->epc,
                'deleted_by' => $user?->name,
            ],
            'user_id' => $user?->id,
        ]);
    }

    /**
     * 4. Tool Missing Notification
     */
    public static function toolMissing($missingTool, $user)
    {
        self::createNotificationForSuperadmins([
            'type' => 'tool_missing',
            'title' => 'Tool Reported Missing',
            'message' => "{$user->name} reported '{$missingTool->nama_barang}' as missing",
            'data' => [
                'missing_tool_id' => $missingTool->id,
                'item_id' => $missingTool->item_id,
                'item_name' => $missingTool->nama_barang,
                'item_epc' => $missingTool->epc,
                'reporter_id' => $user->id,
                'reporter_name' => $user->name,
            ],
            'user_id' => $user->id,
        ]);
    }

    public static function toolReclaimed($missingTool, $user)
    {
        self::createNotificationForSuperadmins([
            'type' => 'tool_reclaimed',
            'title' => 'Missing Tool Reclaimed',
            'message' => "{$user->name} successfully reclaimed '{$missingTool->nama_barang}'",
            'data' => [
                'missing_tool_id' => $missingTool->id,
                'item_id' => $missingTool->item_id,
                'item_name' => $missingTool->nama_barang,
                'item_epc' => $missingTool->epc,
                'reclaimer_id' => $user->id,
                'reclaimer_name' => $user->name,
            ],
            'user_id' => $user->id,
        ]);
    }
}
