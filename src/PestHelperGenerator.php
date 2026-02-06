<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest;

use AceOfAces\Intellipest\Data\PestConfig;
use AceOfAces\Intellipest\Data\TestCaseExtension;
use AceOfAces\Intellipest\Support\Stub;

/**
 * Generates IDE helper file content from a parsed PestConfig.
 *
 * Produces a PHP file with:
 * - Global Pest function stubs (beforeAll, beforeEach, test, it)
 * - Pest\Expectation and Pest\Expectations\OppositeExpectation classes
 * - Test case stub classes with trait use statements
 * - PHPUnit\Framework\TestCase with default traits (if any)
 */
final class PestHelperGenerator
{
    public function generate(PestConfig $config): string
    {
        $sections = [];

        // Global functions namespace (only if test case extensions exist)
        if (count($config->testCaseExtensions) > 0) {
            $testCaseClasses = array_values(array_unique(
                array_map(
                    fn (TestCaseExtension $ext): string => $this->toFQCN($ext->testCase),
                    $config->testCaseExtensions,
                ),
            ));

            $sections[] = "namespace {\n\n".$this->generateGlobalFunctions($testCaseClasses)."\n\n}";
        }

        // Expectation classes (only if expectations exist)
        if (count($config->expectations) > 0) {
            $sections[] = "namespace Pest {\n\n".$this->generateExpectationClass($config->expectations)."\n\n}";
            $sections[] = "namespace Pest\\Expectations {\n\n".$this->generateExpectationClass($config->expectations, true)."\n\n}";
        }

        // Test case stub classes (only for extensions that have traits)
        foreach ($config->testCaseExtensions as $extension) {
            if (count($extension->traits) === 0) {
                continue;
            }

            $parts = explode('\\', $extension->testCase);
            $className = array_pop($parts);
            $namespace = implode('\\', $parts);

            if ($namespace === '' || $className === '') {
                continue;
            }

            $sections[] = "namespace {$namespace} {\n\n".$this->generateTestCaseClass($className, $extension->traits)."\n\n}";
        }

        // Default test case traits (PHPUnit\Framework\TestCase)
        if (count($config->defaultTestCaseTraits) > 0) {
            $sections[] = "namespace PHPUnit\\Framework {\n\n".$this->generateTestCaseClass('TestCase', $config->defaultTestCaseTraits)."\n\n}";
        }

        return "<?php\n\n".implode("\n\n", $sections)."\n";
    }

    /**
     * Generate the global namespace block content with Pest function stubs.
     *
     * @param  list<string>  $testCaseClasses  Fully qualified class names with leading backslash
     */
    private function generateGlobalFunctions(array $testCaseClasses): string
    {
        $unionType = implode('|', $testCaseClasses);

        return Stub::render(
            dirname(__DIR__).'/stubs/global_functions.stub',
            ['unionType' => $unionType],
        );
    }

    /**
     * Generate an Expectation or OppositeExpectation class with @method docblocks.
     *
     * @param  list<string>  $expectations  Custom expectation method names
     */
    private function generateExpectationClass(array $expectations, bool $opposite = false): string
    {
        $methodLines = array_map(
            fn (string $name): string => "     * @method self {$name}()",
            $expectations,
        );

        $className = $opposite ? 'OppositeExpectation' : 'Expectation';

        $lines = [];
        $lines[] = '    /**';
        $lines = array_merge($lines, $methodLines);
        $lines[] = '     */';
        $lines[] = "    class {$className} {}";

        return implode("\n", $lines);
    }

    /**
     * Generate a test case stub class with trait use statements.
     *
     * @param  list<string>  $traits  Fully qualified trait names
     */
    private function generateTestCaseClass(string $className, array $traits): string
    {
        $traitLines = array_map(
            fn (string $trait): string => "        use {$this->toFQCN($trait)};",
            $traits,
        );

        $lines = [];
        $lines[] = "    class {$className}";
        $lines[] = '    {';
        $lines = array_merge($lines, $traitLines);
        $lines[] = '    }';

        return implode("\n", $lines);
    }

    /**
     * Ensure a class name has a leading backslash.
     */
    private function toFQCN(string $className): string
    {
        return str_starts_with($className, '\\') ? $className : "\\{$className}";
    }
}
