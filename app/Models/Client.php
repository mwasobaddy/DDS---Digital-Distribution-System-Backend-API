<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'account_type',
        'country',
        'referral_code',
        'contact_person',
        'billing_info',
    ];

    protected $casts = [
        'billing_info' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'client_id');
    }
}