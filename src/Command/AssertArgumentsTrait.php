<?php

declare(strict_types=1);

namespace BelkaCar\DbStateDumper\Command;

use Symfony\Component\Console\Exception\InvalidArgumentException;

trait AssertArgumentsTrait
{
    private function assertArguments(
        string $configPath,
        string $outputPath
    ): void {
        if (file_exists($configPath) === false) {
            throw new InvalidArgumentException('Configuration file "' . $configPath . '" not found');
        }

        if (file_exists($outputPath) === false) {
            throw new InvalidArgumentException('Output directory "' . $outputPath . '" does not exist');
        }

        if (!is_dir($outputPath)) {
            throw new InvalidArgumentException('The output path "' . $outputPath . '" must be a directory');
        }
    }
}
