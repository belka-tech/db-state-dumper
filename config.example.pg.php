<?php

return [
    'dsn' => 'pgsql:host=127.0.0.1;port=5432;dbname=database',
    'username' => 'login',
    'password' => 'secret',
    'queries' => [
        'activity' => <<<'SQL'
            SELECT
                pid, 
                usename, 
                datname, 
                application_name, 
                state, 
                wait_event_type, 
                wait_event, 
                query,  
                backend_xid, 
                query_start, 
                to_char((now() - query_start), 'HH24:MI:SS') as query_start_time,
                to_char((now() - xact_start), 'HH24:MI:SS') as tr_start_time,
                client_addr
            FROM pg_stat_activity
            WHERE state != 'idle'
            ORDER BY now() - xact_start DESC;
            SQL,
        'locks' => <<<'SQL'
            SELECT
              bl.pid  AS blocked_pid,
              ka.query AS blocking_statement,
              ka.application_name AS blocking_name,
              now() - ka.query_start AS blocking_duration,
              kl.pid  AS blocking_pid,
              a.query AS blocked_statement,
              now() - a.query_start AS blocked_duration
            FROM pg_catalog.pg_locks bl
            JOIN pg_catalog.pg_stat_activity a
              ON a.pid = bl.pid
            JOIN pg_catalog.pg_locks kl
              ON kl.transactionid = bl.transactionid
             AND kl.pid <> bl.pid
            JOIN pg_catalog.pg_stat_activity ka
              ON ka.pid = kl.pid
            WHERE NOT bl.granted
              AND kl.granted;
            SQL,
    ],
];
