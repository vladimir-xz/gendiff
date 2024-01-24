<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Formatters\chooseFormate;

function ifArraysOfSameType($array1, $array2)
{
    if (array_key_exists(0, $array1) && !array_key_exists(0, $array2)) {
        return false;
    } elseif (!array_key_exists(0, $array1) && array_key_exists(0, $array2)) {
        return false;
    }
    return true;
}

function addNewLine($array)
{
    return ['symbol' => '+', 'value' => $array ];
}

function addDeletedLine($array)
{
    return ['symbol' => '-', 'value' => $array ];
}

function addSameLine($array)
{
    return ['symbol' => ' ', 'value' => $array ];
}

function addChangedLine($array)
{
    return ['symbol' => '+/-', 'value' => $array ];
}

function addOldAndNew($old, $new)
{
    return ['symbol' => 'both', 'value' => ['-' => $old ?? 'null', '+' => $new ?? 'null']];
}

function getValueAndSymbol($array)
{
    return ['symbol' => $array['symbol'], 'value' => $array['value']];
}

function compareData($array1, $array2)
{
    $merged = array_merge($array1, $array2);
    ksort($merged);
    $result = array_reduce(array_keys($merged), function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            $acc[$key] = addDeletedLine($array1[$key]);
            return $acc;
        } elseif (!array_key_exists($key, $array1)) {
            $acc[$key] = addNewLine($array2[$key]);
            return $acc;
        } elseif ($array1[$key] === $array2[$key]) {
            $acc[$key] = addSameLine($array1[$key]);
            return $acc;
        } else {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                $acc[$key] = addChangedLine(compareData($array1[$key], $array2[$key]));
                return $acc;
            } else {
                $acc[$key] = addOldAndNew($array1[$key], $array2[$key]);
                return $acc;
            }
        }
    }, []);
    return $result;
}

function makeArrayOfDifferencies($array)
{
    $result = array_map(function ($key, $value) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        if ($symbol === 'both') {
            $deletedValue = ["- {$key}" => $difference['-']];
            $addedValue = ["+ {$key}" => $difference['+']];
            return [...$deletedValue, ...$addedValue];
        } elseif ($symbol === '+/-') {
            return ["  {$key}" => makeArrayOfDifferencies($difference)];
        } else {
            return ["{$symbol} {$key}" => $difference];
        }
    }, array_keys($array), $array);
    return array_merge(...$result);
}

function genDiff($pathToFile1, $pathToFile2, $format)
{
    $firstFile = (array)parseFile($pathToFile1);
    $secondFile = (array)parseFile($pathToFile2);
    $array = compareData($firstFile, $secondFile);
    return chooseFormate($format, $array);
}
