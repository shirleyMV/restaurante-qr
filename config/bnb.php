<?php

return [
    'sandbox_url' => env('BNB_SANDBOX_URL', 'https://sandbox.bnb.com.bo/api'),
    'token_url' => env('BNB_TOKEN_URL', 'https://sandbox.bnb.com.bo/api'),
    'account_id' => env('BNB_ACCOUNT_ID', ''),
    'authorization_id' => env('BNB_AUTHORIZATION_ID', ''),
    'user_key' => env('BNB_USER_KEY', ''),
    'currency' => env('BNB_CURRENCY', 'BOB'),
    'single_use' => env('BNB_SINGLE_USE', true),
];