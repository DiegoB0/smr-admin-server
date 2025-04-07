<?php

// phpcs:ignoreFile

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject; // Add this import
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Permission;

class User extends Authenticatable implements JWTSubject // Implement JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // This is typically the user ID.
    }

    /**
     * Get custom claims for the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
        'roles' => $this->roles->pluck('slug'),
        'permissions' => $this->permissions->pluck('slug'),
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    // Get all permissions via roles
    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            Role::class,
            'id', // FK on roles table...
            'id', // FK on permissions table...
            'id', // Local key on users table...
            'id'  // Local key on roles table...
        )->distinct();
    }

    // Helper to check for a role
    public function hasRole($role)
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    // Helper to check for a permission (through roles)
    public function hasPermission($permission)
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('slug', $permission);
            })
            ->exists();
    }
}
