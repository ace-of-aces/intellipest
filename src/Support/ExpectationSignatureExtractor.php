<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Support;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\UnionType;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

final class ExpectationSignatureExtractor
{
    public function __construct(
        private readonly PrettyPrinter $prettyPrinter = new PrettyPrinter,
    ) {}

    /**
     * @param  list<Arg|VariadicPlaceholder>  $args
     * @return array{list<string>, ?string}
     */
    public function extract(array $args): array
    {
        if (count($args) < 2) {
            return [[], null];
        }

        $callbackArg = $args[1];

        if (! $callbackArg instanceof Arg) {
            return [[], null];
        }

        $callback = $callbackArg->value;

        if (! $callback instanceof Closure && ! $callback instanceof ArrowFunction) {
            return [[], null];
        }

        return [
            array_values(array_map($this->renderParam(...), $callback->params)),
            $this->renderType($callback->returnType),
        ];
    }

    private function renderParam(Param $param): string
    {
        $code = '';

        if ($param->type !== null) {
            $renderedType = $this->renderType($param->type);

            if ($renderedType !== null) {
                $code .= $renderedType.' ';
            }
        }

        if ($param->byRef) {
            $code .= '&';
        }

        if ($param->variadic) {
            $code .= '...';
        }

        if (! $param->var instanceof Variable) {
            return trim($code);
        }

        $variableName = $param->var->name;

        if (! is_string($variableName)) {
            return trim($code);
        }

        $code .= '$'.$variableName;

        if ($param->default !== null) {
            $code .= ' = '.$this->renderDefaultValue($param->default);
        }

        return $code;
    }

    private function renderDefaultValue(Node\Expr $default): string
    {
        if ($default instanceof ConstFetch) {
            $constant = strtolower($default->name->toString());

            if (in_array($constant, ['null', 'true', 'false'], true)) {
                return $constant;
            }
        }

        return $this->prettyPrinter->prettyPrintExpr($default);
    }

    private function renderType(Node|string|null $type): ?string
    {
        if ($type === null) {
            return null;
        }

        if (is_string($type)) {
            return $type;
        }

        if ($type instanceof NullableType) {
            $renderedType = $this->renderType($type->type);

            return $renderedType === null ? null : '?'.$renderedType;
        }

        if ($type instanceof UnionType) {
            return $this->renderTypeList($type->types, '|');
        }

        if ($type instanceof IntersectionType) {
            return $this->renderTypeList($type->types, '&');
        }

        if ($type instanceof Identifier || $type instanceof Name) {
            return $type->toString();
        }

        return null;
    }

    /**
     * @param  array<array-key, Node>  $types
     */
    private function renderTypeList(array $types, string $separator): ?string
    {
        $renderedTypes = [];

        foreach ($types as $type) {
            $renderedType = $this->renderType($type);

            if ($renderedType === null) {
                return null;
            }

            $renderedTypes[] = $renderedType;
        }

        return implode($separator, $renderedTypes);
    }
}
