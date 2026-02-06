<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Commands;

use AceOfAces\IntelliPest\IntelliPest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IntelliPestCommand extends Command
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
            )
            ->addOption(
                'no-expectation-helpers',
                null,
                InputOption::VALUE_NONE,
                'Don\'t generate helper methods for built-in expectations in the output file'
            )
            ->addOption(
                'shush',
                's',
                InputOption::VALUE_NONE,
                'Don\'t show the beautiful header and footer in the console output😔'
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

        $output->writeln('');
    }

    protected function displayFooter(OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('Made with ❤️  by Julian');
        $output->writeln('');
        $output->writeln('> GitHub:  https://github.com/ace-of-aces');
        $output->writeln('> Twitter: https://x.com/julian_center');
        $output->writeln('> Website: https://julian.center');
        $output->writeln('');
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getOption('shush')) {
            return;
        }

        $this->displayHeader($output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = $input->getOption('config');
        $generateMixinExpectations = ! (bool) $input->getOption('no-expectation-helpers');

        if (! file_exists($configPath)) {
            $output->writeln("<error>Config file not found: $configPath</error>");

            return Command::FAILURE;
        }

        $intellipest = new IntelliPest($configPath, $generateMixinExpectations);
        $content = $intellipest->generate();

        $outputPath = $input->getOption('output') ?? $this->resolveDefaultOutputPath();

        if (! str_ends_with($outputPath, '.php')) {
            $output->writeln("<error>Output file must have a .php extension: $outputPath</error>");

            return Command::FAILURE;
        }

        $directory = dirname($outputPath);
        $invalidSegment = $this->findBlockingFileInPath($directory);

        if ($invalidSegment !== null) {
            $output->writeln("<error>Invalid output path – '$invalidSegment' is not a directory</error>");

            return Command::FAILURE;
        }

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($outputPath, $content);

        $output->writeln("<info>✓ Helper file generated: $outputPath</info>");

        if (! $input->getOption('shush')) {
            $this->displayFooter($output);
        }

        return Command::SUCCESS;
    }

    private function resolveDefaultOutputPath(): string
    {
        $outputDir = getenv('INTELLIPEST_OUTPUT_DIR') ?: self::DEFAULT_OUTPUT_DIR;

        return $outputDir.'/'.self::DEFAULT_OUTPUT_FILE;
    }

    private function findBlockingFileInPath(string $directory): ?string
    {
        $path = $directory;

        while ($path !== '' && $path !== '.' && $path !== '/') {
            if (is_file($path)) {
                return $path;
            }

            if (is_dir($path)) {
                return null;
            }

            $path = dirname($path);
        }

        return null;
    }
}
