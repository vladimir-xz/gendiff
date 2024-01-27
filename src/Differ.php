<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Formatters\chooseFormateAndPrint;
use function Functional\sort;

function addNewLine(mixed $array)
{
    return ['symbol' => '+', 'value' => $array ];
}

function addDeletedLine(mixed $array)
{
    return ['symbol' => '-', 'value' => $array ];
}

function addSameLine(mixed $array)
{
    return ['symbol' => ' ', 'value' => $array ];
}

function addChangedLine(mixed $array)
{
    return ['symbol' => '+/-', 'value' => $array ];
}

function addOldAndNew(mixed $old, mixed $new)
{
    return ['symbol' => 'both', 'value' => ['-' => $old ?? 'null', '+' => $new ?? 'null']];
}

function getValueAndSymbol(array $array)
{
    return ['symbol' => $array['symbol'], 'value' => $array['value']];
}

function compareData(array $arrayOne, array $arrayTwo)
{
    $mergedKeys = array_merge(array_keys($arrayOne), array_keys($arrayTwo));
    $sortedKeys = sort(array_unique($mergedKeys), fn ($left, $right) => strcmp($left, $right), true);
    $result = array_map(function ($key) use ($arrayOne, $arrayTwo) {
        if (!array_key_exists($key, $arrayTwo)) {
            return [$key => addDeletedLine($arrayOne[$key])];
        } elseif (!array_key_exists($key, $arrayOne)) {
            return [$key => addNewLine($arrayTwo[$key])];
        } elseif ($arrayOne[$key] === $arrayTwo[$key]) {
            return [$key => addSameLine($arrayTwo[$key])];
        } else {
            if (is_array($arrayOne[$key]) && is_array($arrayTwo[$key])) {
                return [$key => addChangedLine(compareData($arrayOne[$key], $arrayTwo[$key]))];
            } else {
                return [$key => addOldAndNew($arrayOne[$key], $arrayTwo[$key])];
            }
        }
    }, $sortedKeys);
    return array_merge(...$result);
}

function makeArrayFromDifferencies(array $comparedData)
{
    $result = array_map(function ($key, $value) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        if ($symbol === 'both') {
            $deletedValue = ["- {$key}" => $difference['-']];
            $addedValue = ["+ {$key}" => $difference['+']];
            return [...$deletedValue, ...$addedValue];
        } elseif ($symbol === '+/-') {
            return ["  {$key}" => makeArrayFromDifferencies($difference)];
        } else {
            return ["{$symbol} {$key}" => $difference];
        }
    }, array_keys($comparedData), $comparedData);
    return array_merge(...$result);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish')
{
    $firstFile = parseFile($pathToFile1);
    $secondFile = parseFile($pathToFile2);
    $array = compareData($firstFile, $secondFile);
    return chooseFormateAndPrint($format, $array);
}
