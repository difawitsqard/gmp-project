<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    // public function getRolesAttribute()
    // {
    //     $roles = $this->roles()->pluck('name')->toArray();
    //     return $roles ? $roles[0] : null;
    // }

    public function getPermissionArray()
    {
        return $this->getAllPermissions()->mapWithKeys(function ($pr) {
            return [$pr['name'] => true];
        });
    }

    public function scopeFilter($query)
    {
        $columns = ['name', 'email', 'phone', 'address'];
        $query->when(request()->filled('search') ?? false, function ($query) use ($columns) {
            $s = request()->search;
            $query->where(function ($query) use ($columns, $s) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $s . '%');
                }
            });
        });

        //filter by role spetie laravel
        $query->when(request()->filled('role') ?? false, function ($query) {
            $s = request()->role;
            $query->whereHas('roles', function (Builder $query) use ($s) {
                $query->where('name', $s);
            });
        });

        // filter by status
        $query->when(request()->filled('status') ?? false, function ($query) {
            $s = request()->status;
            $query->where('is_active', $s);
        });
    }

    public function scopeSorting($query)
    {
        $query->when(
            request()->filled('sort_field') && request()->filled('sort_order'),
            function ($query) {
                $field = request()->get('sort_field', 'name');
                $order = request()->get('sort_order', 'ascend') === 'ascend' ? 'asc' : 'desc';
                // Abaikan jika kolom takde
                if (in_array($field, array_merge($this->getFillable(), ['created_at', 'updated_at']))) {
                    $query->orderBy($field, $order);
                } else {
                    $query->orderBy('name', 'asc');
                }
            },
            function ($query) {
                $query->orderBy('name', 'asc');
            }
        );
    }
}
