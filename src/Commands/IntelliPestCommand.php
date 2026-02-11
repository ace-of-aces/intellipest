<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Commands;

use AceOfAces\IntelliPest\IntelliPest;
use React\EventLoop\Loop;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

class IntelliPestCommand extends Command
{
    private const DEFAULT_OUTPUT_FILE = '_pest-helper.php';

    private const DEFAULT_OUTPUT_DIR = '.intellipest';

    private const WATCH_INTERVAL_SECONDS = 0.5;

    protected static string $defaultName = 'intellipest';

    private int $lastModificationTime = 0;

    private string $configPath;

    private string $outputPath;

    private bool $shush;

    private bool $watch;

    private bool $generateMixinExpectations;

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
            )
            ->addOption(
                'watch',
                'w',
                InputOption::VALUE_NONE,
                'Watch the input configuration file and regenerate on changes'
            );
    }

    protected function displayHeader(OutputInterface $output): void
    {
        $output->writeln('');

        $terminal = new Terminal;
        $terminalWidth = $terminal->getWidth();

        if ($terminalWidth < 82) {
            $output->writeln('<fg=bright-magenta;options=bold>IntelliPest</>');
            $output->writeln('');

            return;
        }

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
        $output->writeln('Made with ❤️  by Julian Schramm');
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
        $this->setup($input);

        if (! file_exists($this->configPath)) {
            $output->writeln("<error>✗ Config file not found: $this->configPath</error>");

            return Command::FAILURE;
        }

        if (! str_ends_with($this->outputPath, '.php')) {
            $output->writeln("<error>✗ Output file must have a .php extension: $this->outputPath</error>");

            return Command::FAILURE;
        }

        if ($this->watch) {
            return $this->startWatchMode($input, $output);
        }

        return $this->generateHelper($output, ! $this->shush);
    }

    private function setup(InputInterface $input): void
    {
        $this->configPath = $input->getOption('config');
        $this->outputPath = $input->getOption('output') ?? $this->resolveDefaultOutputPath();
        $this->shush = (bool) $input->getOption('shush');
        $this->generateMixinExpectations = ! (bool) $input->getOption('no-expectation-helpers');
        $this->watch = (bool) $input->getOption('watch');
    }

    private function generateHelper(OutputInterface $output, bool $displayFooter): int
    {
        $directory = dirname($this->outputPath);
        $invalidSegment = $this->findBlockingFileInPath($directory);

        if ($invalidSegment !== null) {
            $output->writeln("<error>✗ Invalid output path – '$invalidSegment' is not a directory</error>");

            return Command::FAILURE;
        }

        $intellipest = new IntelliPest($this->configPath, $this->generateMixinExpectations);

        $output->writeln("<info>∘ Analyzing Pest config: $this->configPath</info>");
        $intellipest->analyze();
        $content = $intellipest->generate();

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->outputPath, $content);

        $output->writeln("<info>✓ Helper file generated: $this->outputPath</info>");

        if ($displayFooter) {
            $this->displayFooter($output);
        }

        return Command::SUCCESS;
    }

    private function startWatchMode(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->shush) {
            $output->writeln('');
            $output->writeln('<fg=bright-magenta;options=bold>👀 Watch Mode Enabled</>');
            $output->writeln("<info>Monitoring: $this->configPath</info>");
            $output->writeln("<info>Output:     $this->outputPath</info>");
            $output->writeln('<info>Interval:   '.self::WATCH_INTERVAL_SECONDS.'s</info>');
            $output->writeln('');
            $output->writeln('<comment>Press Ctrl+C to stop watching...</comment>');
            $output->writeln('');
        }

        clearstatcache(true, $this->configPath);
        $this->lastModificationTime = filemtime($this->configPath) ?: 0;
        $this->safeGenerateHelper($input, $output);

        if (getenv('INTELLIPEST_WATCH_TEST_MODE') === '1') {
            return Command::SUCCESS;
        }

        $timer = Loop::addPeriodicTimer(self::WATCH_INTERVAL_SECONDS, function () use ($input, $output): void {
            clearstatcache(true, $this->configPath);
            $currentModificationTime = filemtime($this->configPath);

            if ($currentModificationTime === false) {
                $output->writeln("<error>✗ Unable to read modification time for: $this->configPath</error>");

                return;
            }

            if ($currentModificationTime > $this->lastModificationTime) {
                $this->lastModificationTime = $currentModificationTime;
                $output->writeln('');
                $output->writeln('<info>✓ Change detected in config file</info>');
                $this->safeGenerateHelper($input, $output);
            }
        });

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function () use ($output, $timer): void {
                $this->stopWatchMode($output, $timer);
            });

            pcntl_signal(SIGTERM, function () use ($output, $timer): void {
                $this->stopWatchMode($output, $timer);
            });
        }

        Loop::run();

    return Command::SUCCESS;
    }

    private function stopWatchMode(OutputInterface $output, $timer): void
    {
        $output->writeln('');
        $output->writeln('<info>✓ Watch mode stopped</info>');

        if (! $this->shush) {
            $this->displayFooter($output);
        }

        Loop::cancelTimer($timer);
        Loop::stop();
    }

    private function safeGenerateHelper(InputInterface $input, OutputInterface $output): void
    {
        try {
            $result = $this->generateHelper($output, false);

            if ($result === Command::FAILURE) {
                $output->writeln('<error>✗ Generation failed. Continuing to watch for changes...</error>');
            }
        } catch (\Throwable $exception) {
            $output->writeln('');
            $output->writeln('<error>✗ Error during generation: '.$exception->getMessage().'</error>');
            $output->writeln('<info>∘ Continuing to watch for changes...</info>');
        }
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
