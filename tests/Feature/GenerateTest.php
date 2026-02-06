<?php

use AceOfAces\Intellipest\Intellipest;

/*
|--------------------------------------------------------------------------
| BasicCase: pest()->extend()->in() + expect()->extend()
|--------------------------------------------------------------------------
*/

test('generates correct helper file for BasicCase', function () {
    $intellipest = new Intellipest('tests/Fixtures/BasicCase/Pest.php');
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/BasicCase/HelperResult.php');

    expect($generated)->toBe($expected);
});

/*
|--------------------------------------------------------------------------
| ComplexCase: multiple pest() chains + multiple expect() extensions
|--------------------------------------------------------------------------
*/

test('generates correct helper file for ComplexCase', function () {
    $intellipest = new Intellipest('tests/Fixtures/ComplexCase/Pest.php');
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/ComplexCase/HelperResult.php');

    expect($generated)->toBe($expected);
});

/*
|--------------------------------------------------------------------------
| LegacyUsesCase: uses()->in() + expect()->extend()
|--------------------------------------------------------------------------
*/

test('generates correct helper file for LegacyUsesCase', function () {
    $intellipest = new Intellipest('tests/Fixtures/LegacyUsesCase/Pest.php');
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/LegacyUsesCase/HelperResult.php');

    expect($generated)->toBe($expected);
});
