<?php

use AceOfAces\Intellipest\Intellipest;

/*
|--------------------------------------------------------------------------
| BasicCase: pest()->extend()->in() + expect()->extend()
|--------------------------------------------------------------------------
*/

test('generates correct helper file for BasicCase', function () {
    $intellipest = new Intellipest('tests/Fixtures/BasicCase/Pest.php', false);
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/BasicCase/HelperResult.php');

    expect($generated)->toBe($expected);
});

/*
|--------------------------------------------------------------------------
| BasicCase with mixin expectations helpers enabled
|--------------------------------------------------------------------------
*/

test('generates correct helper file for BasicCase with mixin expectations helpers enabled', function () {
    $intellipest = new Intellipest('tests/Fixtures/BasicCase/Pest.php', true);
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/BasicCase/HelperResultWithExpectations.php');

    expect($generated)->toBe($expected);
});

/*
|--------------------------------------------------------------------------
| ComplexCase: multiple pest() chains + multiple expect() extensions
|--------------------------------------------------------------------------
*/

test('generates correct helper file for ComplexCase', function () {
    $intellipest = new Intellipest('tests/Fixtures/ComplexCase/Pest.php', false);
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
    $intellipest = new Intellipest('tests/Fixtures/LegacyUsesCase/Pest.php', false);
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/LegacyUsesCase/HelperResult.php');

    expect($generated)->toBe($expected);
});

/*
|--------------------------------------------------------------------------
| TraitOnlyCase: pest()->extend() with only a trait and no test class
|--------------------------------------------------------------------------
*/

test('generates correct helper file for TraitOnlyCase', function () {
    $intellipest = new Intellipest('tests/Fixtures/TraitOnlyCase/Pest.php', false);
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/TraitOnlyCase/HelperResult.php');

    expect($generated)->toBe($expected);
});

/*
|--------------------------------------------------------------------------
| TraitOnlyCase with mixin expectations helpers enabled
|--------------------------------------------------------------------------
*/

test('generates correct helper file for TraitOnlyCase with mixin expectations helpers enabled', function () {
    $intellipest = new Intellipest('tests/Fixtures/TraitOnlyCase/Pest.php', true);
    $generated = $intellipest->generate();

    $expected = file_get_contents('tests/Fixtures/TraitOnlyCase/HelperResultWithExpectations.php');

    expect($generated)->toBe($expected);
});