<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DA extends Model
{
    use HasFactory;

    protected $table = 'das';

    protected $fillable = [
        'user_id',
        'referral_code',
        'venture_shares',
        'national_id',
        'dob',
        'gender',
        'country',
        'county',
        'sub_county',
        'ward',
        'address',
        'social_platforms',
        'followers_range',
        'preferred_channel',
        'preferred_wallet_type',
        'wallet_pin_hash',
        'consent_terms',
        'consent_data',
        'consent_ethics',
        'referred_by_da_id',
    ];

    protected $casts = [
        'venture_shares' => 'decimal:2',
        'social_platforms' => 'array',
        'consent_terms' => 'boolean',
        'consent_data' => 'boolean',
        'consent_ethics' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referredDCDs()
    {
        return $this->hasMany(DCD::class, 'referring_da_id');
    }
}