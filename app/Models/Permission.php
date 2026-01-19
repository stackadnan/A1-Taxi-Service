<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['module','action','description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Keep a virtual "name" attribute to maintain compatibility with existing views/tests
    public function getNameAttribute(): string
    {
        return trim(($this->module ?? '') . '.' . ($this->action ?? ''), '.');
    }
} 
