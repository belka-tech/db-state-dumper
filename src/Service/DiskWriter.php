<?php

declare(strict_types=1);

namespace BelkaCar\DbStateDumper\Service;

use DateTimeImmutable;

final class DiskWriter
{
    public function saveString(
        string $outputDirPath,
        string $prefix,
        string $json,
        DateTimeImmutable $datetime
    ): void {
        $directory = $outputDirPath
            . DIRECTORY_SEPARATOR
            . $datetime->format('Y-m-d');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $this->makeFilePath(
            $directory,
            $datetime,
            $prefix,
            'json'
        );

        $file = fopen($filename, 'w');
        fputs($file, $json);
        fclose($file);
    }

    public function saveCsv(
        string $outputDirPath,
        string $prefix,
        array $rows,
        DateTimeImmutable $datetime
    ): void {
        if (empty($rows)) {
            return;
        }

        $directory = $this->makePathAndCreateDir($outputDirPath, $datetime);

        $filename = $this->makeFilePath(
            $directory,
            $datetime,
            $prefix,
            'csv'
        );

        $file = fopen($filename, 'w');

        $this->writeFileHeader($file, $rows);
        $this->writeFileBody($file, $rows);

        fclose($file);
    }

    private function writeFileHeader($file, array $rows)
    {
        fputcsv($file, array_keys($rows[0]));
    }

    private function writeFileBody($file, array $rows)
    {
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }
    }

    private function makePathAndCreateDir(string $outputDirPath, DateTimeImmutable $datetime): string
    {
        $directory = $outputDirPath
            . DIRECTORY_SEPARATOR
            . $datetime->format('Y-m-d');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        return $directory;
    }

    private function makeFilePath(string $directory, DateTimeImmutable $datetime, string $prefix, string $ext): string
    {
        return $directory
            . DIRECTORY_SEPARATOR
            . $datetime->format('Hi')
            . '_'
            . $prefix
            . '.'
            . $ext;
    }
}
