<?php

return [


    /*
    |--------------------------------------------------------------------------
    | TriggerDev Secret Key
    |--------------------------------------------------------------------------
    |
    | The TriggerDev Secret Key is used to call API from TriggerDev.
    | You can find your TriggerDev Secret Key in the TriggerDev dashboard.
    |
    */

    'secret_key' => env('TRIGGERDEV_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | TriggerDev Url Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI where routes from TriggerDev will be served
    | from. The URL built into TriggerDev is used by default; however,
    | you can modify this path as you see fit for your application.
    |
    */

    'path' => env('TRIGGERDEV_PATH', 'triggerdev'),

];
