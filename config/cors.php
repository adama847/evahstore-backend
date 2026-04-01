<?php

return [

    /*

    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // On ajoute 'auth/*' pour autoriser les routes de connexion directes
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/*', 'login', 'logout'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:5174',
        'http://127.0.0.1:5500',
        'https://admin-evahstore.vercel.app',
        'https://evaah-tau.vercel.app',
        'https://admin-evah.vercel.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Passer à true est recommandé pour les APIs modernes
    'supports_credentials' => true,

];
