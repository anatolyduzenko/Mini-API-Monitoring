<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Prometheus Storage Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "redis", "in_memory"
    |
    */

    'driver' => env('PROMETHEUS_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | If using Redis driver, you may customize the Laravel Redis connection.
    |
    */

    'redis_connection' => env('PROMETHEUS_REDIS_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Metrics Groups
    |--------------------------------------------------------------------------
    |
    | Define your metrics classes here.
    |
    */
    'groups' => [
        \AnatolyDuzenko\ConfigurablePrometheus\Metrics\Groups\UserMetrics::class,
        \App\Metrics\EndpointMetrics::class,
        \App\Metrics\JobMetrics::class,
    ],

    'endpoint' => 'prometheus',

];
