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
    $outputPath = '.ide-helper/_pest.php';

    $commandTester->execute([]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('IDE helper file generated');
    expect(file_exists($outputPath))->toBeTrue();
})->with('intellipestCommand');

test('intellipest command accepts custom config path via option', function (CommandTester $commandTester) {
    $outputPath = '.ide-helper/_pest.php';

    $commandTester->execute([
        '--config' => 'tests/Pest.php',
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('IDE helper file generated');
})->with('intellipestCommand');

test('intellipest command fails when config file does not exist', function (CommandTester $commandTester) {
    $commandTester->execute([
        '--config' => 'tests/NonExistent.php',
    ]);

    expect($commandTester->getStatusCode())->toBe(1);
    expect($commandTester->getDisplay())->toContain('Config file not found');
})->with('intellipestCommand');

test('intellipest command writes to custom output path', function (CommandTester $commandTester) {
    $outputPath = sys_get_temp_dir().'/intellipest-test/_pest.php';

    // Clean up before test
    if (file_exists($outputPath)) {
        unlink($outputPath);
    }

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('IDE helper file generated');
    expect(file_exists($outputPath))->toBeTrue();
    expect(file_get_contents($outputPath))->toStartWith('<?php');

    // Clean up
    unlink($outputPath);
    rmdir(dirname($outputPath));
})->with('intellipestCommand');

test('intellipest command creates output directory if it does not exist', function (CommandTester $commandTester) {
    $outputDir = sys_get_temp_dir().'/intellipest-test-'.uniqid();
    $outputPath = $outputDir.'/_pest.php';

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect(is_dir($outputDir))->toBeTrue();
    expect(file_exists($outputPath))->toBeTrue();

    // Clean up
    unlink($outputPath);
    rmdir($outputDir);
})->with('intellipestCommand');
