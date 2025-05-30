<?php

namespace Modules\Auth\Models;

use App\Http\Traits\ArchiveTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements \Illuminate\Contracts\Auth\Access\Authorizable
{
    use HasApiTokens, HasRoles, HasPermissions, HasFactory, Notifiable, SoftDeletes, ArchiveTrait;

    protected $guard_name = 'web';
    protected $fillable = [
        'name',
        'email',
        'country_code',
        'phone',
        'password',
        'gender',
        'profile_image',
        'otp',
        'otp_sent_at',
        'otp_verified_at',
        'otp_expires_at',
        'otp_attempts',
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
}
