<?php

namespace Differ\Differ;

use Differ\Processing;

function genDiff($pathToFile1, $pathToFile2)
{
    $firstFile = Processing\getJsonContent($pathToFile1);
    $secondFile = Processing\getJsonContent($pathToFile2);
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
