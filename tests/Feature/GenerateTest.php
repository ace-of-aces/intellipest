<?php

use AceOfAces\IntelliPest\IntelliPest;
use Tests\Support\Fixtures;

foreach (Fixtures::flat() as $fixture) {
    $caseName = $fixture['case'];
    $configPath = $fixture['configPath'];
    $resultPath = $fixture['resultPath'];
    $mixinExpectations = $fixture['mixinExpectations'];

    $testName = "generates correct helper file for {$caseName}";
    if ($mixinExpectations) {
        $testName .= ' with mixin expectations helpers enabled';
    }

    test($testName, function () use ($configPath, $resultPath, $mixinExpectations) {
        $intellipest = new IntelliPest($configPath, $mixinExpectations);
        $intellipest->analyze();
        $generated = $intellipest->generate();

        $expected = file_get_contents($resultPath);

        expect($generated)->toBe($expected);
    });
}
