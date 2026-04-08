<?php

use AceOfAces\IntelliPest\Data\ClassLikeReference;
use AceOfAces\IntelliPest\Data\ExpectCall;
use AceOfAces\IntelliPest\Data\PestCall;
use AceOfAces\IntelliPest\Data\UsesCall;
use AceOfAces\IntelliPest\Enums\ClassLikeType;
use AceOfAces\IntelliPest\IntelliPest;

/*
|--------------------------------------------------------------------------
| BasicCase: pest()->extend()->in() + expect()->extend()
|--------------------------------------------------------------------------
*/

test('analyze parses pest() call chain from BasicCase fixture', function () {
    $intellipest = new IntelliPest('tests/Fixtures/BasicCase/Pest.php');
    $intellipest->analyze();

    $pestCalls = $intellipest->visitor->getPestCalls();

    expect($pestCalls)->toHaveCount(1);
    expect($pestCalls[0])->toBeInstanceOf(PestCall::class);
    expect($pestCalls[0]->classesAndTraits)->toHaveCount(1);
    expect($pestCalls[0]->classesAndTraits[0])->toBeInstanceOf(ClassLikeReference::class);
    expect($pestCalls[0]->classesAndTraits[0]->name)->toBe('Tests\Fixtures\Stubs\SomeTestCase');
    expect($pestCalls[0]->classesAndTraits[0]->type)->toBe(ClassLikeType::Class_);
    expect($pestCalls[0]->in)->toBe('Feature');
});

test('analyze parses expect() call chain from BasicCase fixture', function () {
    $intellipest = new IntelliPest('tests/Fixtures/BasicCase/Pest.php');
    $intellipest->analyze();

    $expectCalls = $intellipest->visitor->getExpectCalls();

    expect($expectCalls)->toHaveCount(1);
    expect($expectCalls[0])->toBeInstanceOf(ExpectCall::class);
    expect($expectCalls[0]->name)->toBe('toBeOne');
    expect($expectCalls[0]->parameters)->toBe([]);
    expect($expectCalls[0]->returnType)->toBeNull();
});

test('analyze produces no uses() calls from BasicCase fixture', function () {
    $intellipest = new IntelliPest('tests/Fixtures/BasicCase/Pest.php');
    $intellipest->analyze();

    expect($intellipest->visitor->getUsesCalls())->toHaveCount(0);
});

/*
|--------------------------------------------------------------------------
| LegacyUsesCase: uses()->in() + expect()->extend()
|--------------------------------------------------------------------------
*/

test('analyze parses legacy uses() call chain', function () {
    $intellipest = new IntelliPest('tests/Fixtures/LegacyUsesCase/Pest.php');
    $intellipest->analyze();

    $usesCalls = $intellipest->visitor->getUsesCalls();

    expect($usesCalls)->toHaveCount(1);
    expect($usesCalls[0])->toBeInstanceOf(UsesCall::class);
    expect($usesCalls[0]->classesAndTraits)->toHaveCount(2);

    expect($usesCalls[0]->classesAndTraits[0]->name)->toBe('Tests\Fixtures\Stubs\SomeTestCase');
    expect($usesCalls[0]->classesAndTraits[0]->type)->toBe(ClassLikeType::Class_);

    expect($usesCalls[0]->classesAndTraits[1]->name)->toBe('Tests\Fixtures\Stubs\RefreshDatabaseTrait');
    expect($usesCalls[0]->classesAndTraits[1]->type)->toBe(ClassLikeType::Trait_);

    expect($usesCalls[0]->in)->toBe('Feature');
});

test('analyze parses expect() from LegacyUsesCase fixture', function () {
    $intellipest = new IntelliPest('tests/Fixtures/LegacyUsesCase/Pest.php');
    $intellipest->analyze();

    $expectCalls = $intellipest->visitor->getExpectCalls();

    expect($expectCalls)->toHaveCount(1);
    expect($expectCalls[0]->name)->toBe('toBeEmail');
    expect($expectCalls[0]->parameters)->toBe([]);
    expect($expectCalls[0]->returnType)->toBeNull();
});

test('analyze produces no pest() calls from LegacyUsesCase fixture', function () {
    $intellipest = new IntelliPest('tests/Fixtures/LegacyUsesCase/Pest.php');
    $intellipest->analyze();

    expect($intellipest->visitor->getPestCalls())->toHaveCount(0);
});

/*
|--------------------------------------------------------------------------
| ComplexCase: multiple pest() chains + multiple expect() extensions
|--------------------------------------------------------------------------
*/

test('analyze parses multiple pest() call chains', function () {
    $intellipest = new IntelliPest('tests/Fixtures/ComplexCase/Pest.php');
    $intellipest->analyze();

    $pestCalls = $intellipest->visitor->getPestCalls();

    expect($pestCalls)->toHaveCount(2);

    // First chain: pest()->extend(SomeTestCase)->use(RefreshDatabaseTrait)->in('Feature')
    expect($pestCalls[0]->classesAndTraits)->toHaveCount(2);
    expect($pestCalls[0]->classesAndTraits[0]->name)->toBe('Tests\Fixtures\Stubs\SomeTestCase');
    expect($pestCalls[0]->classesAndTraits[0]->type)->toBe(ClassLikeType::Class_);
    expect($pestCalls[0]->classesAndTraits[1]->name)->toBe('Tests\Fixtures\Stubs\RefreshDatabaseTrait');
    expect($pestCalls[0]->classesAndTraits[1]->type)->toBe(ClassLikeType::Trait_);
    expect($pestCalls[0]->in)->toBe('Feature');

    // Second chain: pest()->extend(DuskTestCase)->use(DatabaseMigrationsTrait)->in('Browser')
    expect($pestCalls[1]->classesAndTraits)->toHaveCount(2);
    expect($pestCalls[1]->classesAndTraits[0]->name)->toBe('Tests\Fixtures\Stubs\DuskTestCase');
    expect($pestCalls[1]->classesAndTraits[0]->type)->toBe(ClassLikeType::Class_);
    expect($pestCalls[1]->classesAndTraits[1]->name)->toBe('Tests\Fixtures\Stubs\DatabaseMigrationsTrait');
    expect($pestCalls[1]->classesAndTraits[1]->type)->toBe(ClassLikeType::Trait_);
    expect($pestCalls[1]->in)->toBe('Browser');
});

test('analyze parses multiple expect() extensions', function () {
    $intellipest = new IntelliPest('tests/Fixtures/ComplexCase/Pest.php');
    $intellipest->analyze();

    $expectCalls = $intellipest->visitor->getExpectCalls();

    expect($expectCalls)->toHaveCount(9);

    expect($expectCalls[0]->name)->toBe('toBePositive');
    expect($expectCalls[0]->parameters)->toBe([]);
    expect($expectCalls[0]->returnType)->toBeNull();

    expect($expectCalls[1]->name)->toBe('toBeEmail');
    expect($expectCalls[1]->parameters)->toBe([]);
    expect($expectCalls[1]->returnType)->toBeNull();

    expect($expectCalls[2]->name)->toBe('toHaveLength');
    expect($expectCalls[2]->parameters)->toBe(['int $length = 1']);
    expect($expectCalls[2]->returnType)->toBeNull();

    expect($expectCalls[3]->name)->toBe('toEqualFoo');
    expect($expectCalls[3]->parameters)->toBe([]);
    expect($expectCalls[3]->returnType)->toBe('bool');

    expect($expectCalls[4]->name)->toBe('toContainValues');
    expect($expectCalls[4]->parameters)->toBe(['mixed ...$values']);
    expect($expectCalls[4]->returnType)->toBeNull();

    expect($expectCalls[5]->name)->toBe('toAppendInto');
    expect($expectCalls[5]->parameters)->toBe(['array &$bucket']);
    expect($expectCalls[5]->returnType)->toBe('array');

    expect($expectCalls[6]->name)->toBe('toTransformWith');
    expect($expectCalls[6]->parameters)->toBe(['Closure $transform']);
    expect($expectCalls[6]->returnType)->toBe('mixed');

    expect($expectCalls[7]->name)->toBe('toUseThreshold');
    expect($expectCalls[7]->parameters)->toBe(['int|float $threshold', '?string $message = null']);
    expect($expectCalls[7]->returnType)->toBe('int|false');

    expect($expectCalls[8]->name)->toBe('toMatchAny');
    expect($expectCalls[8]->parameters)->toBe(['string|int $expected', 'array $options = []']);
    expect($expectCalls[8]->returnType)->toBeNull();
});
