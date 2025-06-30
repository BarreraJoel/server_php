<?php

return [
    "host" => $_ENV["DB_HOST"] ?? 'localhost',
    "database" => $_ENV["DB_NAME"] ?? 'server_php',
    "port" => $_ENV["DB_PORT"] ?? '3306',
    "user" => $_ENV["DB_USER"] ?? 'root',
    "password" => $_ENV["DB_PASSWORD"] ?? '',
];
