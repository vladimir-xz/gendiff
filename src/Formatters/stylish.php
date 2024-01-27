<?php

namespace Differ\Formatters\Stylish;

function makeString(mixed $item)
{
    if (!is_array($item)) {
        $jsonFile = json_encode($item, JSON_PRETTY_PRINT, 512);
        if ($jsonFile === false) {
            throw new \Exception('Error when turning value into string');
        }
        $result = trim($jsonFile, "\"");
        return $result;
    }
    return $item;
}

function stylishPrinting(array $comparedArray, string $separator = '    ', int $depth = 0, int $offset = 2)
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        $nextDepth =  $depth + 1;
        if (in_array($key[0], ['+', '-', ' '], true)) {
            $addingWithouOffset = str_repeat($separator, $nextDepth);
            $adding = substr($addingWithouOffset, $offset, null);
        } else {
            $adding = str_repeat($separator, $nextDepth);
        }
        $resultKey = "{$adding}{$key}";
        if (is_array($value)) {
            $convertedValue = stylishPrinting($value, $separator, $nextDepth, $offset);
        } else {
            $convertedValue = makeString($value);
        }
        $result = "{$resultKey}: {$convertedValue}";
        return $result;
    }, array_keys($comparedArray), $comparedArray);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}
