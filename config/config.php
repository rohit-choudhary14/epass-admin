<?php
// PHP 7.2 compatible config
return [
    'db' => [
        'dsn' => 'pgsql:host=10.130.8.95;port=5432;dbname=jaipur_new', // change dbname
        'user' => 'postgres',
        'pass' => '1', // change
        'options' => [
            // PDO options if needed
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ],
    'app' => [
        'base_url' => '/hc-epass-mvc/public', // adjust if different
        'pdf_path' => __DIR__ . '/../storage/pdf'
    ]
];
