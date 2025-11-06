<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'default_referral_code',
        'is_active',
        'notification_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'notification_settings' => 'array',
    ];

    /**
     * Get the default admin for referrals
     */
    public static function getDefaultAdmin()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Generate a unique referral code
     */
    public static function generateReferralCode()
    {
        do {
            $code = 'ADMIN_' . strtoupper(Str::random(8));
        } while (static::where('default_referral_code', $code)->exists());

        return $code;
    }
}
