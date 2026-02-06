<?php

use AceOfAces\Intellipest\Commands\IntellipestCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

dataset('intellipestCommand', [
    'commandTester' => function () {
        $application = new Application;
        $application->addCommand(new IntellipestCommand);
        $command = $application->find('intellipest');

        return new CommandTester($command);
    },
]);

test('intellipest command runs successfully with default config path', function (CommandTester $commandTester) {
    $commandTester->execute([]);

    expect($commandTester->getStatusCode())->toBe(0);
})->with('intellipestCommand');

test('intellipest command accepts custom config path via option', function (CommandTester $commandTester) {
    $commandTester->execute([
        '--config' => 'tests/Pest.php',
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
})->with('intellipestCommand');

test('intellipest command fails when config file does not exist', function (CommandTester $commandTester) {
    $commandTester->execute([
        '--config' => 'tests/NonExistent.php',
    ]);

    expect($commandTester->getStatusCode())->toBe(1);
    expect($commandTester->getDisplay())->toContain('Config file not found');
})->with('intellipestCommand');
