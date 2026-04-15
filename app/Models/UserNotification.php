<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UserNotification extends Model
{
    use HasFactory;

    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id', 'title', 'message', 'is_read', 'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Create notification for all admin users
     */
    public static function createForAdmins(string $title, string $message)
    {
        try {
            // Support both schemas: with or without users.is_admin column.
            static $hasIsAdminColumn = null;
            if ($hasIsAdminColumn === null) {
                $hasIsAdminColumn = Schema::hasColumn((new User())->getTable(), 'is_admin');
            }

            $adminUsersQuery = User::query();

            if ($hasIsAdminColumn) {
                $adminUsersQuery
                    ->where('is_admin', true)
                    ->orWhereHas('roles', function ($q) {
                        $q->whereHas('permissions');
                    });
            } else {
                $adminUsersQuery->whereHas('roles', function ($q) {
                    $q->where('name', 'Super Admin')
                      ->orWhereHas('permissions');
                });
            }

            $adminUsers = $adminUsersQuery->get();

            $now = now();
            $recentWindow = 30; // seconds

            foreach ($adminUsers as $admin) {
                // Avoid creating duplicate notifications within the recent window
                $exists = self::where('user_id', $admin->id)
                    ->where('title', $title)
                    ->where('message', $message)
                    ->where('created_at', '>=', $now->copy()->subSeconds($recentWindow))
                    ->exists();

                if ($exists) {
                    continue;
                }

                self::create([
                    'user_id' => $admin->id,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => false,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('UserNotification::createForAdmins failed', [
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }
}