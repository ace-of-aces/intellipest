<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest;

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

final class Intellipest
{
    public function __construct(
        public string $configPath = 'tests/Pest.php'
    ) {}

    public function analyze(): void
    {
        $code = file_get_contents($this->configPath);
        $parser = (new ParserFactory)->createForHostVersion();
        try {
            $ast = $parser->parse($code);
        } catch (\Error $error) {
            echo "Parse error: {$error->getMessage()}\n";

            return;
        }

        $dumper = new NodeDumper;
        echo $dumper->dump($ast)."\n";
    }
}
