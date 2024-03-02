<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\getNode;

const SYMBOLS = [
    'added' => '+',
    'deleted' => '-',
    'same' => ' ',
    'nested' => ' ',
    'old and new' => '',
];

function createEmptySpace(int $depth, int $offset = 0, string $separator = '    ')
{
    $emptySpaceWithoutSymbol = str_repeat($separator, $depth);
    if (is_null($offset)) {
        return $emptySpaceWithoutSymbol;
    }
    return substr($emptySpaceWithoutSymbol, $offset, null);
}

function stringify(mixed $item, int $depth): string
{
    if (!is_array($item)) {
        $itemString = is_string($item) ? $item : var_export($item, true);
        return $itemString === 'NULL' ? 'null' : $itemString;
    }
    $emptySpace = createEmptySpace($depth);
    $lines = array_map(function ($key, $value) use ($depth) {
        $nextDepth = $depth + 1;
        $emptySpace = createEmptySpace($nextDepth);
        $valueString = stringify($value, $nextDepth);
        return "{$emptySpace}{$key}: {$valueString}";
    }, array_keys($item), $item);
    $linesWithBrackets = ['{', ...$lines, "{$emptySpace}}"];
    return implode("\n", $linesWithBrackets);
}

function format(array $comparedData, int $depth = 0): string
{
    $emptySpace = createEmptySpace($depth);
    $lines = array_map(function ($data) use ($depth) {
        ['type' => $type, 'key' => $key, 'value' => $value] = getNode($data);
        $symbol = SYMBOLS[$type];
        $nextDepth = $depth + 1;
        $offsetForSymbol = 2;
        $emptySpace = createEmptySpace($nextDepth, $offsetForSymbol);
        $keyForPrint = "{$emptySpace}{$symbol} {$key}";
        if ($type === 'old and new') {
            $oldAndNew = array_map(fn ($item) => stringify($item, $nextDepth), $value);
            return "{$emptySpace}- {$key}: {$oldAndNew['old']}\n{$emptySpace}+ {$key}: {$oldAndNew['new']}";
        } elseif ($type === 'nested') {
            $valueString = format($value, $nextDepth);
        } else {
            $valueString = stringify($value, $nextDepth);
        }
        return "{$keyForPrint}: {$valueString}";
    }, $comparedData);
    $linesWithBrackets = ['{', ...$lines, "{$emptySpace}}"];
    return implode("\n", $linesWithBrackets);
}
