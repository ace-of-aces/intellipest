<?php

declare(strict_types=1);

use AceOfAces\IntelliPest\Commands\IntelliPestCommand;
use AceOfAces\IntelliPest\Support\Stub;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

dataset('intellipestCommand', [
    'commandTester' => function () {
        $application = new Application;
        $application->addCommand(new IntelliPestCommand);
        $command = $application->find('intellipest');

        return new CommandTester($command);
    },
]);

afterEach(function () {
    cleanDirectory(testOutputDir());
});

test('intellipest command runs successfully with default config path', function (CommandTester $commandTester) {
    $commandTester->execute([]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('Helper file generated');
    expect(testOutputPath())->toBeFile();
})->with('intellipestCommand');

test('intellipest command accepts custom config path via option', function (CommandTester $commandTester) {
    $commandTester->execute([
        '--config' => 'tests/Pest.php',
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('Helper file generated');
})->with('intellipestCommand');

test('intellipest command fails when config file does not exist', function (CommandTester $commandTester) {
    $commandTester->execute([
        '--config' => 'tests/NonExistent.php',
    ]);

    expect($commandTester->getStatusCode())->toBe(1);
    expect($commandTester->getDisplay())->toContain('Config file not found');
})->with('intellipestCommand');

test('intellipest command writes to custom output path', function (CommandTester $commandTester) {
    $outputPath = testOutputDir().'/custom/_my-pest-helper.php';

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('Helper file generated');
    expect($outputPath)->toBeFile();
    expect(file_get_contents($outputPath))->toStartWith('<?php');
})->with('intellipestCommand');

test('intellipest command creates output directory if it does not exist', function (CommandTester $commandTester) {
    $outputPath = testOutputDir().'/nested/deep/_pest-helper.php';

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect(dirname($outputPath))->toBeDirectory();
    expect($outputPath)->toBeFile();
})->with('intellipestCommand');

test('intellipest command fails when output file does not end with .php', function (CommandTester $commandTester) {
    $outputPath = testOutputDir().'/helper.txt';

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(1);
    expect($commandTester->getDisplay())->toContain('Output file must have a .php extension');
})->with('intellipestCommand');

test('intellipest command fails when output file has no extension', function (CommandTester $commandTester) {
    $outputPath = testOutputDir().'/helper';

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(1);
    expect($commandTester->getDisplay())->toContain('Output file must have a .php extension');
})->with('intellipestCommand');

test('intellipest command fails when parent path contains an existing file', function (CommandTester $commandTester) {
    $blockerFile = testOutputDir().'/blockerfile';

    if (! is_dir(testOutputDir())) {
        mkdir(testOutputDir(), 0o755, true);
    }

    file_put_contents($blockerFile, 'blocked');

    $outputPath = $blockerFile.'/nested.php';

    $commandTester->execute([
        '--output' => $outputPath,
    ]);

    expect($commandTester->getStatusCode())->toBe(1);
    expect($commandTester->getDisplay())->toContain('is not a directory');
})->with('intellipestCommand');

test('intellipest command generates helper file with mixin expectations helpers when the --expectation-helpers option is used', function (CommandTester $commandTester) {
    $commandTester->execute([
        '--expectation-helpers' => true,
    ]);

    $expectationHelperContent = Stub::render(dirname(__DIR__).'/../stubs/mixin_expectations.stub');

    expect($commandTester->getStatusCode())->toBe(0);
    expect(testOutputPath())->toBeFile();
    expect(file_get_contents(testOutputPath()))->toContain($expectationHelperContent);
})->with('intellipestCommand');

test('intellipest command generates helper file without mixin expectations helpers by default', function (CommandTester $commandTester) {
    $commandTester->execute([]);

    $expectationHelperContent = Stub::render(dirname(__DIR__).'/../stubs/mixin_expectations.stub');

    expect($commandTester->getStatusCode())->toBe(0);
    expect(testOutputPath())->toBeFile();
    expect(file_get_contents(testOutputPath()))->not()->toContain($expectationHelperContent);
})->with('intellipestCommand');

test('intellipest command displays simple header on narrow terminals', function (CommandTester $commandTester) {
    putenv('COLUMNS=70');

    $commandTester->execute([]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('IntelliPest');
    expect($commandTester->getDisplay())->not()->toContain('████████╗');
})->with('intellipestCommand');

test('intellipest command displays ASCII art header on wide terminals', function (CommandTester $commandTester) {
    putenv('COLUMNS=100');

    $commandTester->execute([]);

    expect($commandTester->getStatusCode())->toBe(0);
    expect($commandTester->getDisplay())->toContain('████████╗');
})->with('intellipestCommand');

test('intellipest command generates file on initial watch run', function (CommandTester $commandTester) {
    putenv('INTELLIPEST_WATCH_TEST_MODE=1');
    $configPath = __DIR__.'/../Fixtures/BasicCase/Pest.php';
    $outputPath = testOutputDir().'/watch-test-helper.php';

    $commandTester->execute([
        '--config' => $configPath,
        '--output' => $outputPath,
        '--watch' => true,
    ]);

    expect($outputPath)->toBeFile();
    expect(file_get_contents($outputPath))->toStartWith('<?php');
})->with('intellipestCommand');

test('intellipest command displays watch mode info when enabled', function (CommandTester $commandTester) {
    putenv('INTELLIPEST_WATCH_TEST_MODE=1');
    $configPath = __DIR__.'/../Fixtures/BasicCase/Pest.php';
    $outputPath = testOutputDir().'/watch-info-helper.php';

    $commandTester->execute([
        '--config' => $configPath,
        '--output' => $outputPath,
        '--watch' => true,
    ]);

    expect($commandTester->getDisplay())->toContain('Watch Mode Enabled');
    expect($commandTester->getDisplay())->toContain('Monitoring:');
    expect($commandTester->getDisplay())->toContain('Interval:');
})->with('intellipestCommand');

test('intellipest command suppresses watch mode info with --shush', function (CommandTester $commandTester) {
    putenv('INTELLIPEST_WATCH_TEST_MODE=1');
    $configPath = __DIR__.'/../Fixtures/BasicCase/Pest.php';
    $outputPath = testOutputDir().'/watch-shush-helper.php';

    $commandTester->execute([
        '--config' => $configPath,
        '--output' => $outputPath,
        '--watch' => true,
        '--shush' => true,
    ]);

    expect($commandTester->getDisplay())->not()->toContain('Watch Mode Enabled');
})->with('intellipestCommand');
