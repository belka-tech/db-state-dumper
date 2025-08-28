<?php

declare(strict_types=1);

namespace BelkaCar\DbStateDumper\Service;

use PDO;

final class Database
{
    private const OPTIONS = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    private PDO $pdo;

    public function __construct(
        string $dsn,
        string $username,
        string $password
    ) {
        $this->pdo = new PDO(
            $dsn,
            $username,
            $password,
            self::OPTIONS
        );
    }

    /**
     * @return array|bool
     */
    public function execute(string $query)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
