<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notify New Device
    |--------------------------------------------------------------------------
    |
    | Here you define whether to receive notifications when logging from a new device.
    |
     */

    'notify' => env('AUTHENTICATION_LOG_NOTIFY', true),

    /*
    |--------------------------------------------------------------------------
    | IP Stack API key
    |--------------------------------------------------------------------------
    |
    | Used for location
    |
     */

    'ipstack_key' => env('ipstack_key', null),

    /*
    |--------------------------------------------------------------------------
    | Old Logs Clear
    |--------------------------------------------------------------------------
    |
    | When the clean-command is executed, all authentication logs older than
    | the number of days specified here will be deleted.
    |
     */

    'older' => 365,

];
