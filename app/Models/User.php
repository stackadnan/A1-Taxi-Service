<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Roles relationship (many-to-many)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check whether the user has a role by name
     */
    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return $this->roles()->whereIn('name', $role)->exists();
        }

        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Whether the user is an admin (flag or role)
     */
    public function isAdmin(): bool
    {
        if (array_key_exists('is_admin', $this->attributes)) {
            return (bool) $this->attributes['is_admin'];
        }

        return $this->hasRole('Super Admin');
    }

    /**
     * Check permission by name (role-based)
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin shortcut
        if ($this->isAdmin()) return true;

        // Expect permission as module.action (e.g., 'user.view')
        $parts = explode('.', $permission, 2);
        if (count($parts) < 2) return false;
        [$module, $action] = $parts;

        // Check via roles -> permissions (module/action columns)
        return $this->roles()->whereHas('permissions', function($q) use($module, $action){
            $q->where('module', $module)->where('action', $action);
        })->exists();
    }

    public function hasAnyPermission(array $perms): bool
    {
        foreach ($perms as $p) {
            if ($this->hasPermission($p)) return true;
        }
        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

}
