<?php

return [

    'default_establishment' => 'jaipur',

    // 'dbs' => [
    //     'P' => [
    //         'dsn'  => 'pgsql:host=10.130.8.95;port=5432;dbname=jaipur',
    //         'user' => 'postgres',
    //         'pass' => '1',
    //     ],
    //     'B' => [
    //         'dsn'  => 'pgsql:host=10.130.8.95;port=5432;dbname=jaipur_new',
    //         'user' => 'postgres',
    //         'pass' => '1',
    //     ],
    // ],
    'dbs' => [
        'P' => [
            'dsn'  => 'pgsql:host=localhost;port=5433;dbname=jaipur',
            'user' => 'postgres',
            'pass' => '1234',
        ],
        'B' => [
            'dsn'  => 'pgsql:host=localhost;port=5433;dbname=jaipur_new',
            'user' => 'postgres',
            'pass' => '1234',
        ],
    ],

    'db_options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],

    'app' => [
        'base_url' => '/hc-epass-mvc/public',
        'pdf_path' => __DIR__ . '/../storage/pdf'
    ]
];
