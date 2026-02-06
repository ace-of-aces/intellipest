<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest\Commands;

use AceOfAces\Intellipest\Intellipest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IntellipestCommand extends Command
{
    private const DEFAULT_OUTPUT_FILE = '_pest.php';

    private const DEFAULT_OUTPUT_DIR = '.ide-helper';

    protected static $defaultName = 'intellipest';

    protected function configure(): void
    {
        $this
            ->setName('intellipest')
            ->setDescription('Generate IDE helpers from a Pest.php configuration')
            ->setHelp('This command allows you to analyze a Pest.php configuration file and generate IDE helper files based on its contents.')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'Path to the Pest.php configuration file',
                'tests/Pest.php'
            )
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_REQUIRED,
                'Path to write the generated IDE helper file',
            );
    }

    protected function displayHeader(OutputInterface $output): void
    {
        $output->writeln('');

        $art = [
            '██╗███╗   ██╗████████╗███████╗██╗     ██╗     ██╗██████╗ ███████╗███████╗████████╗',
            '██║████╗  ██║╚══██╔══╝██╔════╝██║     ██║     ██║██╔══██╗██╔════╝██╔════╝╚══██╔══╝',
            '██║██╔██╗ ██║   ██║   █████╗  ██║     ██║     ██║██████╔╝█████╗  ███████╗   ██║   ',
            '██║██║╚██╗██║   ██║   ██╔══╝  ██║     ██║     ██║██╔═══╝ ██╔══╝  ╚════██║   ██║   ',
            '██║██║ ╚████║   ██║   ███████╗███████╗███████╗██║██║     ███████╗███████║   ██║   ',
            '╚═╝╚═╝  ╚═══╝   ╚═╝   ╚══════╝╚══════╝╚══════╝╚═╝╚═╝     ╚══════╝╚══════╝   ╚═╝   ',
        ];

        // Vaporwave gradient (from the Laravel installer❤️)
        $gradient = [213, 177, 141, 105, 69, 39];

        foreach ($art as $index => $line) {
            $color = $gradient[$index];
            $output->writeln("\e[38;5;{$color}m{$line}\e[0m");
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->displayHeader($output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = $input->getOption('config');

        if (! file_exists($configPath)) {
            $output->writeln("<error>Config file not found: $configPath</error>");

            return Command::FAILURE;
        }

        $intellipest = new Intellipest($configPath);
        $content = $intellipest->generate();

        $outputPath = $input->getOption('output') ?? $this->resolveDefaultOutputPath();
        $directory = dirname($outputPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($outputPath, $content);

        $output->writeln("<info>IDE helper file generated: $outputPath</info>");

        return Command::SUCCESS;
    }

    private function resolveDefaultOutputPath(): string
    {
        $outputDir = getenv('INTELLIPEST_OUTPUT_DIR') ?: self::DEFAULT_OUTPUT_DIR;

        return $outputDir.'/'.self::DEFAULT_OUTPUT_FILE;
    }
}
