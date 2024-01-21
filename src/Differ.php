<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Stylish\printing;

function ifArraysOfSameType($array1, $array2)
{
    if (array_key_exists(0, $array1) && !array_key_exists(0, $array2)) {
        return false;
    } elseif (!array_key_exists(0, $array1) && array_key_exists(0, $array2)) {
        return false;
    }
    return true;
}

function compareArrays($array1, $array2)
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
                $acc["  {$key}"] = compareArrays($array1[$key], $array2[$key]);
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

function genDiff($pathToFile1, $pathToFile2, $format)
{
    $firstFile = (array)parseFile($pathToFile1);
    $secondFile = (array)parseFile($pathToFile2);
    $array = compareArrays($firstFile, $secondFile);
    switch ($format) {
        case 'stylish':
            return (printing($array));
    }
}
