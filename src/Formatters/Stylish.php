<?php

namespace Differ\Differ;

use function Differ\Differ\getNode;

function stringify(mixed $item, int $depth = 0, int $offset = 2, string $separator = '    '): string
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
    $result = array_map(function ($key, $value) use ($depth, $separator, $offset) {
        $nextDepth = $depth + 1;
        $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
        $valueString = stringify($value, $depth + 1, $offset);
        return "{$emptySpace}{$key}: {$valueString}";
    }, array_keys($item), $item);
    $resultWithBrackets = ['{', ...$result, "{$emptySpace}}"];
    return implode("\n", $resultWithBrackets);
}

function makeStylish(mixed $comparedData, int $depth = 0)
{
    $iter = function ($comparedData) use (&$iter, $depth) {
        $result = array_map(function ($data) use ($iter, $depth) {
            ['status' => $status, 'symbol' => $symbol, 'difference' => $difference] = getNode($data);
            $key = key($difference);
            $value = current($difference);
            if ($status === 'old and new') {
                return $iter($value);
            }
            $keyWithSymbol = "{$symbol}{$key}";
            $nextDepth = $depth + 1;
            if ($status === 'changed') {
                $valueString = makeStylish($value, $nextDepth);
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
