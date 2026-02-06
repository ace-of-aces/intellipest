<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected static $defaultName = 'hello';

    protected function configure(): void
    {
        $this
            ->setName('hello')
            ->setDescription('Say hello to the user')
            ->setHelp('This command outputs a greeting message')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'The name of the person to greet',
                'World'
            )
            ->addOption(
                'uppercase',
                'u',
                InputOption::VALUE_NONE,
                'Convert the greeting to uppercase'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $message = "Hello, $name! Welcome to IntelliPest.";

        if ($input->getOption('uppercase')) {
            $message = strtoupper($message);
        }

        $output->writeln($message);

        return Command::SUCCESS;
    }
}
