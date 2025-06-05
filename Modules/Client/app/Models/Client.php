<?php

namespace Modules\Client\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Models\User;

// use Modules\Client\Database\Factories\ClientFactory;

class Client extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'address',
        'financing_type',
        'job',
        'salary',
        'work_nature',
        'nationality',
        'other_income_sources',
        'religion',
        'gender',
        'national_id',
        'has_previous_loan',
        'previous_loan_name',
        'previous_loan_value',
   ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
