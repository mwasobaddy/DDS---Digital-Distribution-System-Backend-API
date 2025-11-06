<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'client_id',
        'title',
        'campaign_type',
        'description',
        'product_url',
        'explainer_video_url',
        'objective',
        'content_safety',
        'business_types',
        'budget',
        'rate_per_scan',
        'status',
        'target_counties',
        'target_regions',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'target_counties' => 'array',
        'business_types' => 'array',
        'target_regions' => 'array',
        'budget' => 'decimal:2',
        'rate_per_scan' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function client()
    {
        // campaigns.client_id stores the user id of the client account
        return $this->belongsTo(User::class, 'client_id');
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function totalScans()
    {
        return $this->scans()->count();
    }

    public function totalSpent()
    {
        return $this->scans()->sum('earnings_amount');
    }
}