<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'epc',
        'nama_barang',
        'user_id',
        'status'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Default attributes
    protected $attributes = [
        'status' => 'available'
    ];

    // Relasi dengan User yang meminjam
    public function borrower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi dengan UserDetail yang meminjam
    public function borrowerDetail()
    {
        return $this->belongsTo(UserDetail::class, 'user_id', 'user_id');
    }

    // Relasi dengan MissingTools
    public function missingTools()
    {
        return $this->hasMany(MissingTools::class, 'item_id');
    }

    // Relasi dengan MissingTool yang sedang pending
    public function activeMissingTool()
    {
        return $this->hasOne(MissingTools::class, 'item_id')->where('status', 'pending');
    }

    // Scope untuk item yang tersedia
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    // Scope untuk item yang sedang dipinjam
    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
    }

    // Scope untuk item yang tidak dipinjam (available atau out_of_stock)
    public function scopeNotBorrowed($query)
    {
        return $query->whereIn('status', ['available', 'out_of_stock']);
    }

    // Scope untuk item yang hilang
    public function scopeMissing($query)
    {
        return $query->where('status', 'missing');
    }

    // Scope untuk item yang tidak hilang
    public function scopeNotMissing($query)
    {
        return $query->where('status', '!=', 'missing');
    }

    // Scope untuk item yang out of stock
    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock');
    }

    // Accessor untuk status text yang readable
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'available' => 'Available',
            'borrowed' => 'Borrowed',
            'missing' => 'Missing',
            'out_of_stock' => 'Out of Stock',
            default => 'Unknown'
        };
    }

    // Accessor untuk status badge class
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'available' => 'bg-success',
            'borrowed' => 'bg-warning',
            'missing' => 'bg-dark',
            'out_of_stock' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Method untuk check apakah item bisa dipinjam
    public function canBeBorrowed()
    {
        return $this->status === 'available';
    }

    // Method untuk check apakah item sedang hilang
    public function isMissing()
    {
        return $this->status === 'missing';
    }

    // Method untuk check apakah item sedang dipinjam
    public function isBorrowed()
    {
        return $this->status === 'borrowed';
    }

    // Method untuk check apakah item tersedia
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    // Method untuk check apakah item out of stock
    public function isOutOfStock()
    {
        return $this->status === 'out_of_stock';
    }

    // IMPROVED: Method untuk check apakah user adalah admin/superadmin yang tidak perlu koin
    public function userNeedsKoin($user)
    {
        if (!$user) {
            Log::warning('userNeedsKoin: User is null');
            return true;
        }

        // Pastikan user memiliki role
        if (!isset($user->role) || empty($user->role)) {
            Log::warning('userNeedsKoin: User role is empty', ['user_id' => $user->id]);
            return true;
        }

        // EXPANDED: Admin roles - lebih comprehensive
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

        $userRole = trim($user->role);
        $isAdmin = in_array($userRole, $adminRoles, true); // strict comparison

        Log::info('userNeedsKoin check', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'is_admin' => $isAdmin,
            'needs_koin' => !$isAdmin
        ]);

        return !$isAdmin;
    }

    // IMPROVED: Method untuk check user secara statis (untuk boot method)
    private static function isUserAdmin($user)
    {
        if (!$user) {
            Log::warning('isUserAdmin: User is null');
            return false;
        }

        if (!isset($user->role) || empty($user->role)) {
            Log::warning('isUserAdmin: User role is empty', ['user_id' => $user->id]);
            return false;
        }

        // EXPANDED: Admin roles - lebih comprehensive  
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

        $userRole = trim($user->role);
        $isAdmin = in_array($userRole, $adminRoles, true); // strict comparison

        Log::info('isUserAdmin check', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'is_admin' => $isAdmin
        ]);

        return $isAdmin;
    }

    // Method untuk meminjam item dengan logika koin
    public function borrowItem($user)
    {
        if (!$this->canBeBorrowed()) {
            throw new \Exception('Item tidak dapat dipinjam saat ini.');
        }

        Log::info('borrowItem initiated', [
            'item_id' => $this->id,
            'item_name' => $this->nama_barang,
            'user_id' => $user->id,
            'user_role' => $user->role ?? 'no_role'
        ]);

        // Cek apakah user perlu koin
        if ($this->userNeedsKoin($user)) {
            $userDetail = $user->userDetail;
            if (!$userDetail || $userDetail->koin <= 0) {
                Log::warning('borrowItem: Insufficient coins', [
                    'user_id' => $user->id,
                    'current_koin' => $userDetail ? $userDetail->koin : 'no_detail'
                ]);
                throw new \Exception('Koin tidak mencukupi untuk meminjam item.');
            }

            // Kurangi koin jika bukan admin/superadmin
            $oldKoin = $userDetail->koin;
            $userDetail->decrement('koin', 1);

            Log::info('borrowItem: Coin deducted', [
                'user_id' => $user->id,
                'old_koin' => $oldKoin,
                'new_koin' => $userDetail->fresh()->koin,
                'item_id' => $this->id
            ]);
        } else {
            Log::info('borrowItem: Admin user - no coin deduction', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'item_id' => $this->id
            ]);
        }

        // Update status item
        $this->update([
            'status' => 'borrowed',
            'user_id' => $user->id
        ]);

        Log::info('borrowItem completed', [
            'item_id' => $this->id,
            'new_status' => $this->status,
            'new_user_id' => $this->user_id
        ]);

        return true;
    }

    // Method untuk mengembalikan item dengan logika koin
    public function returnItem()
    {
        if (!$this->isBorrowed()) {
            throw new \Exception('Item tidak sedang dipinjam.');
        }

        $borrower = $this->borrower;

        Log::info('returnItem initiated', [
            'item_id' => $this->id,
            'item_name' => $this->nama_barang,
            'borrower_id' => $borrower ? $borrower->id : null,
            'borrower_role' => $borrower ? ($borrower->role ?? 'no_role') : null
        ]);

        // Kembalikan koin jika user bukan admin/superadmin
        if ($borrower && $this->userNeedsKoin($borrower)) {
            $userDetail = $borrower->userDetail;
            if ($userDetail) {
                $oldKoin = $userDetail->koin;
                $userDetail->increment('koin', 1);

                Log::info('returnItem: Coin returned', [
                    'user_id' => $borrower->id,
                    'old_koin' => $oldKoin,
                    'new_koin' => $userDetail->fresh()->koin,
                    'item_id' => $this->id
                ]);
            }
        } else {
            Log::info('returnItem: Admin user or no borrower - no coin return', [
                'borrower_id' => $borrower ? $borrower->id : null,
                'borrower_role' => $borrower ? $borrower->role : null,
                'item_id' => $this->id
            ]);
        }

        // Update status item
        $this->update([
            'status' => 'available',
            'user_id' => null
        ]);

        Log::info('returnItem completed', [
            'item_id' => $this->id,
            'new_status' => $this->status,
            'new_user_id' => $this->user_id
        ]);

        return true;
    }

    // Method untuk mark item as missing
    public function markAsMissing()
    {
        Log::info('markAsMissing initiated', [
            'item_id' => $this->id,
            'current_status' => $this->status
        ]);

        $this->update([
            'status' => 'missing',
        ]);

        return true;
    }

    // Method untuk mark item as found (from missing to available)
    public function markAsFound()
    {
        if ($this->status === 'missing') {
            Log::info('markAsFound initiated', [
                'item_id' => $this->id
            ]);

            $this->update([
                'status' => 'available',
                'user_id' => null
            ]);
        }

        return true;
    }

    // Method untuk mark item as out of stock
    public function markAsOutOfStock()
    {
        Log::info('markAsOutOfStock initiated', [
            'item_id' => $this->id,
            'current_status' => $this->status
        ]);

        $this->update([
            'status' => 'out_of_stock',
            'user_id' => null
        ]);

        return true;
    }

    // ================================
    // IMPROVED BOOT METHOD - UNTUK RASPBERRY PI
    // ================================
    protected static function boot()
    {
        parent::boot();

        // Event listener untuk update dari Raspberry Pi
        static::updated(function ($item) {
            if ($item->isDirty('user_id') || $item->isDirty('status')) {
                $originalUserId = $item->getOriginal('user_id');
                $newUserId = $item->user_id;
                $originalStatus = $item->getOriginal('status');
                $newStatus = $item->status;

                Log::info('Item updated via boot method', [
                    'item_id' => $item->id,
                    'original_user_id' => $originalUserId,
                    'new_user_id' => $newUserId,
                    'original_status' => $originalStatus,
                    'new_status' => $newStatus,
                    'trigger_source' => 'likely_raspberry_pi'
                ]);

                // PEMINJAMAN: available -> borrowed dengan user_id
                if ($newStatus === 'borrowed' && $newUserId && $originalStatus !== 'borrowed') {
                    Log::info('Boot method: Processing borrowing action', [
                        'item_id' => $item->id,
                        'new_user_id' => $newUserId
                    ]);

                    $user = User::find($newUserId);
                    if ($user) {
                        // IMPROVED: Use static method untuk konsistensi
                        $isAdmin = static::isUserAdmin($user);

                        if (!$isAdmin) {
                            // User biasa - kurangi koin
                            $userDetail = UserDetail::where('user_id', $newUserId)->first();
                            if ($userDetail && $userDetail->koin > 0) {
                                $oldKoin = $userDetail->koin;
                                $userDetail->decrement('koin', 1);

                                Log::info('Boot method: Coin deducted for regular user', [
                                    'user_id' => $newUserId,
                                    'user_role' => $user->role,
                                    'old_koin' => $oldKoin,
                                    'new_koin' => $userDetail->fresh()->koin,
                                    'item_id' => $item->id
                                ]);
                            } else {
                                Log::warning('Boot method: User has no coins or user detail', [
                                    'user_id' => $newUserId,
                                    'has_detail' => $userDetail ? 'yes' : 'no',
                                    'current_koin' => $userDetail ? $userDetail->koin : 'no_detail'
                                ]);
                            }
                        } else {
                            Log::info('Boot method: Admin user detected - NO coin deduction', [
                                'user_id' => $newUserId,
                                'user_role' => $user->role,
                                'item_id' => $item->id,
                                'admin_protection' => 'ACTIVE'
                            ]);
                        }
                    } else {
                        Log::error('Boot method: User not found', [
                            'user_id' => $newUserId,
                            'item_id' => $item->id
                        ]);
                    }
                }

                // PENGEMBALIAN: borrowed -> available tanpa user_id
                if ($originalStatus === 'borrowed' && $newStatus === 'available' && !$newUserId && $originalUserId) {
                    Log::info('Boot method: Processing return action', [
                        'item_id' => $item->id,
                        'original_user_id' => $originalUserId
                    ]);

                    $oldUser = User::find($originalUserId);
                    if ($oldUser) {
                        // IMPROVED: Use static method untuk konsistensi
                        $isAdmin = static::isUserAdmin($oldUser);

                        if (!$isAdmin) {
                            // User biasa - kembalikan koin
                            $userDetail = UserDetail::where('user_id', $originalUserId)->first();
                            if ($userDetail) {
                                $oldKoin = $userDetail->koin;
                                $userDetail->increment('koin', 1);

                                Log::info('Boot method: Coin returned for regular user', [
                                    'user_id' => $originalUserId,
                                    'user_role' => $oldUser->role,
                                    'old_koin' => $oldKoin,
                                    'new_koin' => $userDetail->fresh()->koin,
                                    'item_id' => $item->id
                                ]);
                            } else {
                                Log::warning('Boot method: User detail not found for coin return', [
                                    'user_id' => $originalUserId,
                                    'item_id' => $item->id
                                ]);
                            }
                        } else {
                            Log::info('Boot method: Admin user detected - NO coin return', [
                                'user_id' => $originalUserId,
                                'user_role' => $oldUser->role,
                                'item_id' => $item->id,
                                'admin_protection' => 'ACTIVE'
                            ]);
                        }
                    } else {
                        Log::error('Boot method: Previous user not found', [
                            'original_user_id' => $originalUserId,
                            'item_id' => $item->id
                        ]);
                    }
                }

                // ADDITIONAL: Log any other status changes for debugging
                if ($originalStatus !== $newStatus && !in_array($newStatus, ['borrowed', 'available'])) {
                    Log::info('Boot method: Other status change detected', [
                        'item_id' => $item->id,
                        'from_status' => $originalStatus,
                        'to_status' => $newStatus,
                        'user_change' => $originalUserId !== $newUserId ? 'yes' : 'no'
                    ]);
                }
            }
        });
    }
}
