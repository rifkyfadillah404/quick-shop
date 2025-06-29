<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asset Optimization Settings
    |--------------------------------------------------------------------------
    */

    'optimization' => [
        'enabled' => env('ASSET_OPTIMIZATION', true),
        
        'css' => [
            'minify' => env('CSS_MINIFY', true),
            'combine' => env('CSS_COMBINE', true),
        ],
        
        'js' => [
            'minify' => env('JS_MINIFY', true),
            'combine' => env('JS_COMBINE', true),
        ],
        
        'images' => [
            'lazy_loading' => env('IMAGE_LAZY_LOADING', true),
            'webp_conversion' => env('IMAGE_WEBP_CONVERSION', false),
            'compression' => env('IMAGE_COMPRESSION', 80),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CDN Settings
    |--------------------------------------------------------------------------
    */

    'cdn' => [
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL', ''),
        'assets' => ['css', 'js', 'images'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => env('ASSET_CACHE', true),
        'duration' => env('ASSET_CACHE_DURATION', 3600), // 1 hour
        'version' => env('ASSET_VERSION', '1.0.0'),
    ],
];
