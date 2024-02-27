<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\getNode;

const SYMBOLS = [
    'added' => '+ ',
    'deleted' => '- ',
    'same' => '  ',
    'changed' => '  ',
    'old and new' => '',
];

function stringify(mixed $item, int $depth, int $offset = 2, string $separator = '    '): string
{
    if (is_string($item)) {
        return $item;
    } elseif (!is_array($item)) {
        $jsonFile = json_encode($item, JSON_PRETTY_PRINT, 512);
        if ($jsonFile === false) {
            throw new \Exception('Error when turning value into string');
        }
        return trim($jsonFile, "\"");
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
            ['status' => $status, 'difference' => $difference] = getNode($data);
            $key = key($difference);
            $value = current($difference);
            $symbol = SYMBOLS[$status];
            $keyWithSymbol = "{$symbol}{$key}";
            $nextDepth = $depth + 1;
            if ($status === 'old and new') {
                return $iter($value);
            } elseif ($status === 'changed') {
                $valueString = format($value, $nextDepth);
            } else {
                $offsetWithoutSymbol = 0;
                $valueString = stringify($value, $nextDepth, $offsetWithoutSymbol);
            }
            return [$keyWithSymbol => $valueString];
        }, $comparedData);
        return array_merge(...$result);
    };
    return stringify($iter($comparedData), $depth);
}
