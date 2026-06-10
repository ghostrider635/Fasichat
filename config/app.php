<?php
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'fasichat_classroom',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'upload' => [
        'directory' => __DIR__ . '/../uploads',
        'max_size' => 20 * 1024 * 1024,
        'allowed_mime' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/mp4',
            'video/mpeg',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
    ],
    'security' => [
        'session_cookie_secure' => false,
        'session_cookie_httponly' => true,
        'session_use_only_cookies' => true,
    ],
];
