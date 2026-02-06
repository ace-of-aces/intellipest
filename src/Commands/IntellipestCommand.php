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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configPath = $input->getOption('config');

        if (! file_exists($configPath)) {
            $output->writeln("<error>Config file not found: $configPath</error>");

            return Command::FAILURE;
        }

        $intellipest = new Intellipest($configPath);
        $intellipest->analyze();

        return Command::SUCCESS;
    }
}
