<?php

declare(strict_types=1);

namespace Tests\Support;

final class Fixtures
{
    /**
     * @return list<array{
     *     case: string,
     *     configPath: string,
     *     results: list<array{
     *         path: string,
     *         mixinExpectations: bool
     *     }>
     * }>
     */
    public static function all(): array
    {
        return [
            [
                'case' => 'BasicCase',
                'configPath' => 'tests/Fixtures/BasicCase/Pest.php',
                'results' => [
                    [
                        'path' => 'tests/Fixtures/BasicCase/HelperResult.php',
                        'mixinExpectations' => false,
                    ],
                    [
                        'path' => 'tests/Fixtures/BasicCase/HelperResultWithExpectations.php',
                        'mixinExpectations' => true,
                    ],
                ],
            ],
            [
                'case' => 'ComplexCase',
                'configPath' => 'tests/Fixtures/ComplexCase/Pest.php',
                'results' => [
                    [
                        'path' => 'tests/Fixtures/ComplexCase/HelperResult.php',
                        'mixinExpectations' => false,
                    ],
                ],
            ],
            [
                'case' => 'LegacyUsesCase',
                'configPath' => 'tests/Fixtures/LegacyUsesCase/Pest.php',
                'results' => [
                    [
                        'path' => 'tests/Fixtures/LegacyUsesCase/HelperResult.php',
                        'mixinExpectations' => false,
                    ],
                ],
            ],
            [
                'case' => 'TraitOnlyCase',
                'configPath' => 'tests/Fixtures/TraitOnlyCase/Pest.php',
                'results' => [
                    [
                        'path' => 'tests/Fixtures/TraitOnlyCase/HelperResult.php',
                        'mixinExpectations' => false,
                    ],
                    [
                        'path' => 'tests/Fixtures/TraitOnlyCase/HelperResultWithExpectations.php',
                        'mixinExpectations' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<array{
     *     case: string,
     *     configPath: string,
     *     resultPath: string,
     *     mixinExpectations: bool
     * }>
     */
    public static function flat(): array
    {
        $flat = [];

        foreach (self::all() as $fixture) {
            foreach ($fixture['results'] as $result) {
                $flat[] = [
                    'case' => $fixture['case'],
                    'configPath' => $fixture['configPath'],
                    'resultPath' => $result['path'],
                    'mixinExpectations' => $result['mixinExpectations'],
                ];
            }
        }

        return $flat;
    }
}
