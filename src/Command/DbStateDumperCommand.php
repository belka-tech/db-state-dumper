<?php

declare(strict_types=1);

namespace BelkaCar\DbStateDumper\Command;

use BelkaCar\DbStateDumper\Service\Database;
use BelkaCar\DbStateDumper\Service\DiskWriter;
use DateTimeImmutable;
use Psr\Log\LogLevel;
use Solodkiy\ConsoleLoggerWithTime\ConsoleLoggerWithTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DbStateDumperCommand extends Command
{
    use AssertArgumentsTrait;

    protected static $defaultName = 'dump';

    protected function configure(): void
    {
        $this
            ->addArgument('config', InputArgument::REQUIRED, 'Path to config file')
            ->addArgument('output', InputArgument::REQUIRED, 'Path to output dir');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $logger = new ConsoleLoggerWithTime($output, [
            LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
        ]);
        $logger->info('Running...');

        $configPath = $input->getArgument('config');
        $outputPath = $input->getArgument('output');

        $this->assertArguments($configPath, $outputPath);

        $config = $this->getConfiguration($configPath);
        $writer = new DiskWriter();
        $database = new Database($config['dsn'], $config['username'], $config['password']);

        foreach ($config['queries'] as $prefix => $query) {
            $logger->debug('Performing query with key: ' . $prefix);

            $start = microtime(true);
            $datetime = new DateTimeImmutable();
            $rows = $database->execute($query);
            $logger->info('Query with key "' . $prefix . '" has a duration ' . number_format(microtime(true) - $start, 2) . ' sec');

            $writer->saveCsv(
                $outputPath,
                $prefix,
                $rows,
                $datetime
            );
        }

        $logger->info('Finished');

        return Command::SUCCESS;
    }

    private function getConfiguration(
        string $configPath
    ): array {
        $config = include $configPath;

        if (!\is_array($config)) {
            throw new InvalidArgumentException('The configuration file must be a valid PHP array');
        }

        if (
            !isset($config['dsn'])
            || !isset($config['username'])
            || !isset($config['password'])
            || !isset($config['queries'])
        ) {
            throw new InvalidArgumentException(
                'Configuration params: "dsn", "username", "password" and "queries" are required'
            );
        }

        if (!\is_array($config['queries'])) {
            throw new InvalidArgumentException('Configuration key "queries" must be an array of SQL queries');
        }

        return $config;
    }
}
