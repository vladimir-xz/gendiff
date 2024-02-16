<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\getValueAndSymbol;

function makeString(mixed $item, string $separator = '    ', int $depth = 0)
{
    if (!is_array($item)) {
        $jsonFile = json_encode($item, JSON_PRETTY_PRINT, 512);
        if ($jsonFile === false) {
            throw new \Exception('Error when turning value into string');
        }
        $result = trim($jsonFile, "\"");
        return $result;
    }
    $emptySpace = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth) {
        $nextDepth =  $depth + 1;
        $emptySpace = str_repeat($separator, $nextDepth);
        $resultKey = "{$emptySpace}{$key}";
        $convertedValue = makeString($value, $separator, $nextDepth);
        $result = "{$resultKey}: {$convertedValue}";
        return $result;
    }, array_keys($item), $item);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$emptySpace}}";
}

function makeStringUsingInterfaces(array $comparedData, string $separator = '    ', int $depth = 0, int $offset = 2)
{
    $emptySpace = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        $nextDepth =  $depth + 1;
        $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
        if ($symbol === 'both') {
            $deletedValue = makeString($difference['-'], $separator, $nextDepth);
            $addedValue = makeString($difference['+'], $separator, $nextDepth);
            return "{$emptySpace}- {$key}: {$deletedValue}\n{$emptySpace}+ {$key}: {$addedValue}";
        } elseif ($symbol === '+/-') {
            $convertedValue = makeStringUsingInterfaces($difference, $separator, $nextDepth);
            return "{$emptySpace}  {$key}: {$convertedValue}";
        } else {
            $valueString = makeString($difference, $separator, $nextDepth);
            return "{$emptySpace}{$symbol} {$key}: {$valueString}";
        }
    }, array_keys($comparedData), $comparedData);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$emptySpace}}";
}
