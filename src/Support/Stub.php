<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest\Support;

final class Stub
{
    /**
     * Render a stub file with the given placeholder replacements.
     *
     * @param  array<string, string>  $replacements
     */
    public static function render(string $stubPath, ?array $replacements = null): string
    {
        $content = file_get_contents($stubPath);

        if ($content === false) {
            throw new \RuntimeException("Stub file not found: {$stubPath}");
        }

        if ($replacements === null) {
            return $content;
        }

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{ {$key} }}", $value, $content);
        }

        return $content;
    }
}
