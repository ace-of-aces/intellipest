<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Concerns;

use Symfony\Component\Console\Input\InputInterface;

trait HasTypedCommandOptions
{
    protected function getStringOption(InputInterface $input, string $name): string
    {
        /** @var mixed $value */
        $value = $input->getOption($name);

        if (! is_string($value)) {
            throw new \UnexpectedValueException("The '{$name}' option must be a string.");
        }

        return $value;
    }

    protected function getNullableStringOption(InputInterface $input, string $name): ?string
    {
        /** @var mixed $value */
        $value = $input->getOption($name);

        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new \UnexpectedValueException("The '{$name}' option must be a string or null.");
        }

        return $value;
    }

    protected function getBoolOption(InputInterface $input, string $name): bool
    {
        /** @var mixed $value */
        $value = $input->getOption($name);

        if (! is_bool($value)) {
            throw new \UnexpectedValueException("The '{$name}' option must be a boolean.");
        }

        return $value;
    }
}
