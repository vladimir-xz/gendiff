<?php

namespace Differ\Differ;

use function Differ\Processing\parseFile;

function FirstgenDiff($pathToFile1, $pathToFile2, $depth = 0)
{
    $firstFile = (array)parseFile($pathToFile1);
    $secondFile = (array)parseFile($pathToFile2);
    $mergedFiles = array_merge($secondFile, $firstFile);
    ksort($mergedFiles);
    $result = array_map(function ($key, $value) use ($firstFile, $secondFile) {
        $stringValueOne = is_bool($value) ? var_export($value, true) : $value;
        if (!array_key_exists($key, $secondFile)) {
            return "  + {$key}: {$stringValueOne}";
        } elseif (!array_key_exists($key, $firstFile)) {
            return "  - {$key}: {$stringValueOne}";
        } elseif ($value !== $secondFile[$key]) {
            $stringValueTwo = is_bool($value) ? var_export($secondFile[$key], true) : $secondFile[$key];
            return "  - {$key}: $stringValueOne\n  + {$key}: $stringValueTwo";
        } else {
            return "    {$key}: $stringValueOne";
        }
    }, array_keys($mergedFiles), array_values($mergedFiles));
    $stringResult = implode("\n", $result);
    return "{\n{$stringResult}\n}\n";
}

function ifArraysOfSameType($array1, $array2)
{
    if (array_key_exists(0, $array1) && !array_key_exists(0, $array2)) {
        return false;
    } elseif (!array_key_exists(0, $array1) && array_key_exists(0, $array2)) {
        return false;
    }
    return true;
}

function lets($array1, $array2)
{
    $merged = array_merge($array1, $array2);
    if (!ifArraysOfSameType($array1, $array2)) {
        return $merged;
    }
    ksort($merged);
    $result = array_reduce(array_keys($merged), function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            $acc["- {$key}"] = ($array1[$key]);
            return $acc;
        } elseif (!array_key_exists($key, $array1)) {
            $acc["+ {$key}"] = ($array2[$key]);
            return $acc;
        } elseif ($array1[$key] === $array2[$key]) {
            $acc["  {$key}"] = ($array1[$key]);
            return $acc;
        } else {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                $acc["  {$key}"] = lets($array1[$key], $array2[$key]);
                return $acc;
            } else {
                $acc["- {$key}"] = ($array1[$key]);
                $acc["+ {$key}"] = ($array2[$key]);
                return $acc;
            }
        }
    }, []);
    return $result;
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
        if (is_array($value)) {
            $value = printing($value, $separator, $depth, $adding);
        }
        $result .= ": {$value}";
        return $result;
    }, array_keys($array), $array);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}

function genDiff($pathToFile1, $pathToFile2)
{
    $firstFile = (array)parseFile($pathToFile1);
    $secondFile = (array)parseFile($pathToFile2);
    $array = lets($firstFile, $secondFile);
    $result = printing($array);
    return $result;
}
