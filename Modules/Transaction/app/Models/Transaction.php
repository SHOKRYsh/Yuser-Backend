<?php

namespace Modules\Transaction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Models\User;
use Modules\Client\Models\Client;

class Transaction extends Model
{
    use HasFactory;

     protected $fillable = [
        'client_id',
        'frontline_liaison_officer_id',
        'main_case_handler_id',
        'financial_officer_id',
        'executive_director_id',
        'legal_supervisor_id',
        'quality_assurance_officer_id',
        'bank_liaison_officer_id',
        'current_status',
        'status_history'
    ];

    protected $casts = [
        'status_history' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function frontlineLiaisonOfficer()
    {
        return $this->belongsTo(User::class, 'frontline_liaison_officer_id');
    }

    public function mainCaseHandler()
    {
        return $this->belongsTo(User::class, 'main_case_handler_id');
    }

    public function financialOfficer()
    {
        return $this->belongsTo(User::class, 'financial_officer_id');
    }

    public function executiveDirector()
    {
        return $this->belongsTo(User::class, 'executive_director_id');
    }

    public function legalSupervisor()
    {
        return $this->belongsTo(User::class, 'legal_supervisor_id');
    }

    public function qualityAssuranceOfficer()
    {
        return $this->belongsTo(User::class, 'quality_assurance_officer_id');
    }

    public function bankLiaisonOfficer()
    {
        return $this->belongsTo(User::class, 'bank_liaison_officer_id');
    }

}
