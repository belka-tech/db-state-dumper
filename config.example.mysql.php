<?php

return [
    'dsn' => 'pgsql:host=127.0.0.1;port=3306;dbname=database',
    'username' => 'login',
    'password' => 'secret',
    'queries' => [
        'activity' => <<<'SQL'
            SELECT
                ID AS pid,
                USER AS usename,
                DB AS datname,
                COMMAND AS state,
                INFO AS query,
                TIME AS query_start_time
            FROM INFORMATION_SCHEMA.PROCESSLIST
            WHERE COMMAND != 'Sleep'
            ORDER BY TIME DESC;
            SQL,
    ],
];
