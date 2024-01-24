<?php

namespace Differ\Formatters\Stylish;

function makeString(mixed $item)
{
    if (!is_array($item)) {
        $result = trim(json_encode($item, JSON_PRETTY_PRINT, 512), "\"");
        return $result;
    }
    return $item;
}

function printing(array $comparedArray, string $separator = '    ', int $depth = 0, int $offset = 2)
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        $depth += 1;
        if (in_array($key[0], ['+', '-', ' '])) {
            $adding = str_repeat($separator, $depth);
            $adding = substr($adding, $offset, null);
        } else {
            $adding = str_repeat($separator, $depth);
        }
        $result = "{$adding}{$key}";
        if (is_array($value)) {
            $convertedValue = printing($value, $separator, $depth, $offset);
        } else {
            $convertedValue = makeString($value);
        }
        $result .= ": {$convertedValue}";
        return $result;
    }, array_keys($comparedArray), $comparedArray);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}
