<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Partner Program Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the Startiz Labs Partner Portal.
    | Commission rates, referral codes, and tracking parameters.
    |
    */

    // Default commission rate (20%) - configurable per partner in partner_profiles
    'default_commission_rate' => (float) env('PARTNER_DEFAULT_COMMISSION_RATE', 20.00),

    // Currency for commissions and payouts
    'currency' => env('PARTNER_CURRENCY', 'INR'),

    // Prefix for unique referral codes (e.g. STZ-000001)
    'referral_prefix' => env('PARTNER_REFERRAL_PREFIX', 'STZ'),

    // Prefix for payout reference numbers
    'payout_prefix' => env('PARTNER_PAYOUT_PREFIX', 'STZ-PAYOUT'),

    // Prefix for commission reference numbers
    'commission_prefix' => env('PARTNER_COMMISSION_PREFIX', 'STZ-COM'),

    // Referral cookie lifetime in days
    'cookie_lifetime_days' => (int) env('PARTNER_COOKIE_LIFETIME_DAYS', 30),
];
