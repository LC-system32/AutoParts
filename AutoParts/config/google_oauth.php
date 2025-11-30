<?php

return [
    'client_id'     => 'google_client_id',
    'client_secret' => 'google_client_secret',
    'redirect_uri'  => 'http://localhost/auth/google/callback',
    'scopes'        => [
        'openid',
        'email',
        'profile',
    ],
];