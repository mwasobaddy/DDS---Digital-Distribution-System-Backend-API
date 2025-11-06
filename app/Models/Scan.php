<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'qr_code',
        'dcd_id',
        'campaign_id',
        'device_id',
        'ip_address',
        'user_agent',
        'earnings_amount',
        'earnings_processed',
        'location',
    ];

    protected $casts = [
        'earnings_processed' => 'boolean',
        'location' => 'array',
        'earnings_amount' => 'decimal:2',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function dcd()
    {
        return $this->belongsTo(DCD::class);
    }
}