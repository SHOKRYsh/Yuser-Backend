<?php

namespace Modules\Auth\Models;


use App\Http\Traits\ArchiveTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class SuperAdmin extends Authenticatable implements \Illuminate\Contracts\Auth\Access\Authorizable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable, SoftDeletes, ArchiveTrait;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender'
    ];

     protected $hidden = [
        'password',
        'remember_token',
    ];
}
