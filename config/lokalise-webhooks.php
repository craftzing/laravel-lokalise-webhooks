<?php

return [

    /*
    |-----------------------------------------------------------------------------------------------
    | X-Secret header
    |-----------------------------------------------------------------------------------------------
    |
    | Here's where you specify the X-Secret for your Lokalise webhook integration.
    | We use it to verify that incoming requests originate from the webhook
    | integration you have set up in the Lokalise project settings.
    |
    | @see https://app.lokalise.com/settings/{yourProjectId}/#integrations/rest-webhook
    |
    */

    'x_secret' => env('LOKALIZE_X_SECRET'),

    /*
    |-----------------------------------------------------------------------------------------------
    | IP restrictions
    |-----------------------------------------------------------------------------------------------
    |
    | As Lokalise does not sign requests, the only way to ensure requests are coming from Lokalise
    | is to set up IP restrictions. By default, only requests coming from a known Lokalise IP
    | have access to the webhook handling route. You can turn this off for development
    | purposes, but we highly discourage doing so on production environments.
    |
    | @see https://docs.lokalise.com/en/articles/3184756-webhooks
    |
    | If your application sits behind a load balancer or any intermediary (reverse) proxy,
    | make sure to list it as a trusted proxy so we can access the real end-client IP.
    |
    | @see https://laravel.com/docs/8.x/requests#configuring-trusted-proxies
    |
    */

    'enable_ip_restrictions' => env('LOKALIZE_ENABLE_IP_RESTRICTIONS', true),

];
