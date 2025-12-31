<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_id',
        'name',
        'role',
        'email',
        'phone',
        'image',
        'lang',
        'status',
        'password',
        'visibility',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'visibility' => 'json',
        'business_id' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'shop-owner') return true;
        [$module, $action] = explode('.', $permission);
        return isset($this->visibility[$module][$action]) && $this->visibility[$module][$action] == "1";
    }

    public function hasAnyPermission(array $permissions)
    {
        if ($this->role === 'shop-owner') return true;

        $visibility = $this->visibility ?? [];
        foreach ($permissions as $permission) {
            [$module, $action] = explode('.', $permission);

            if (!empty($visibility[$module][$action]) && $visibility[$module][$action] == "1") {
                return true;
            }
        }

        return false;
    }
}
