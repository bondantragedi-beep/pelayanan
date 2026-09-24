<?php

use App\Models\Sekolah;
use App\Models\User;
use App\Models\Verifikator;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | "sekolah" dipakai untuk login Operator Sekolah (NPSN + password).
    | "verifikator" dipakai untuk login Verifikator Dinas maupun Admin
    | (Admin login lewat akun verifikator yang sama, dibedakan lewat
    | kolom is_admin pada tabel verifikators).
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'sekolah' => [
            'driver' => 'session',
            'provider' => 'sekolah',
        ],

        'verifikator' => [
            'driver' => 'session',
            'provider' => 'verifikators',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],

        'sekolah' => [
            'driver' => 'eloquent',
            'model' => Sekolah::class,
        ],

        'verifikators' => [
            'driver' => 'eloquent',
            'model' => Verifikator::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
