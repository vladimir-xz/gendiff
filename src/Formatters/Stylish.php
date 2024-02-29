<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\getNode;

const SYMBOLS = [
    'added' => '+',
    'deleted' => '-',
    'same' => ' ',
    'changed' => ' ',
    'old and new' => '',
];

function stringify(mixed $item, int $depth, int $offset = 2, string $separator = '    '): string
{
    if (!is_array($item)) {
        $itemString = is_string($item) ? $item : var_export($item, true);
        return $itemString === 'NULL' ? 'null' : $itemString;
    }
    $emptySpace = str_repeat($separator, $depth);
    $lines = array_map(function ($key, $value) use ($depth, $separator, $offset) {
        $nextDepth = $depth + 1;
        $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
        $valueString = stringify($value, $nextDepth, $offset);
        return "{$emptySpace}{$key}: {$valueString}";
    }, array_keys($item), $item);
    $linesWithBrackets = ['{', ...$lines, "{$emptySpace}}"];
    return implode("\n", $linesWithBrackets);
}

function format(array $comparedData, int $depth = 0): string
{
    $iter = function ($comparedData) use (&$iter, $depth) {
        $result = array_map(function ($data) use ($iter, $depth) {
            ['type' => $type, 'key' => $key, 'difference' => $difference] = getNode($data);
            $symbol = SYMBOLS[$type];
            $keyWithSymbol = "{$symbol} {$key}";
            $nextDepth = $depth + 1;
            $offsetWithoutSymbol = 0;
            if ($type === 'old and new') {
                return $iter($difference);
            } elseif ($type === 'changed') {
                $valueString = format($difference, $nextDepth);
            } else {
                $valueString = stringify(current($difference), $nextDepth, $offsetWithoutSymbol);
            }
            return [$keyWithSymbol => $valueString];
        }, $comparedData);
        return array_merge(...$result);
    };
    return stringify($iter($comparedData), $depth);
}
