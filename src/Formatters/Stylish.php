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
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth) {
        $nextDepth =  $depth + 1;
        $adding = str_repeat($separator, $nextDepth);
        $resultKey = "{$adding}{$key}";
        $convertedValue = makeString($value, $separator, $nextDepth);
        $result = "{$resultKey}: {$convertedValue}";
        return $result;
    }, array_keys($item), $item);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}

function makeStringUsingInterfaces(array $comparedData, string $separator = '    ', int $depth = 0, int $offset = 2)
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        $nextDepth =  $depth + 1;
        $adding = substr(str_repeat($separator, $nextDepth), $offset, null);
        if ($symbol === 'both') {
            $deletedValue = makeString($difference['-'], $separator, $nextDepth);
            $addedValue = makeString($difference['+'], $separator, $nextDepth);
            return "{$adding}- {$key}: {$deletedValue}\n{$adding}+ {$key}: {$addedValue}";
        } elseif ($symbol === '+/-') {
            $convertedValue = makeStringUsingInterfaces($difference, $separator, $nextDepth);
            return "{$adding}  {$key}: {$convertedValue}";
        } else {
            $valueString = makeString($difference, $separator, $nextDepth);
            return "{$adding}{$symbol} {$key}: {$valueString}";
        }
    }, array_keys($comparedData), $comparedData);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}
