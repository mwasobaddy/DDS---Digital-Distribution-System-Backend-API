<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DCD extends Model
{
    use HasFactory;

    protected $table = 'dcds';

    protected $fillable = [
        'id',
        'user_id',
        'referral_code',
        'qr_code',
        'referring_da_id',
        'status',
        'national_id',
        'dob',
        'gender',
        'country',
        'county',
        'sub_county',
        'ward',
        'business_address',
        'gps_location',
        'business_name',
        'business_types',
        'daily_foot_traffic',
        'operating_hours',
        'preferred_campaign_types',
        'music_genres',
        'content_safe',
        'preferred_wallet_type',
        'wallet_pin_hash',
        'consent_terms',
        'consent_data',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'gps_location' => 'array',
        'business_types' => 'array',
        'preferred_campaign_types' => 'array',
        'music_genres' => 'array',
        'content_safe' => 'boolean',
        'consent_terms' => 'boolean',
        'consent_data' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referringDA()
    {
        return $this->belongsTo(DA::class, 'referring_da_id');
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }
}