<?php

namespace Differ\Stylish;

function makeStringIfNotArray(mixed $item)
{
    if (!is_array($item)) {
        $result = trim(json_encode($item, JSON_PRETTY_PRINT), "\"");
        return $result;
    }
    return $item;
}

function printing($array, $separator = ' ', $depth = 0, $adding = '')
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $adding) {
        $depth += 4;
        if (in_array($key[0], ['+', '-', ' '])) {
            $adding = str_repeat($separator, $depth - 2);
        } else {
            $adding = str_repeat($separator, $depth);
        }
        $result = "{$adding}{$key}";
        $convertedValue = makeStringIfNotArray($value);
        if (is_array($value)) {
            $value = printing($value, $separator, $depth, $adding);
            $convertedValue = $value;
        }
        $result .= ": {$convertedValue}";
        return $result;
    }, array_keys($array), $array);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}
