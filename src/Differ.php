<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Parsers\makePathAbsolute;
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
    return ['symbol' => 'both', 'value' => ['-' => $old, '+' => $new]];
}

function getValueAndSymbol(array $array)
{
    return ['symbol' => $array['symbol'], 'value' => $array['value']];
}

function compare(array $dataOne, array $dataTwo)
{
    $mergedKeys = array_merge(array_keys($dataOne), array_keys($dataTwo));
    $sortedKeys = sort(array_unique($mergedKeys), fn ($left, $right) => strcmp($left, $right), true);
    $result = array_map(function ($key) use ($dataOne, $dataTwo) {
        if (!array_key_exists($key, $dataTwo)) {
            return [$key => addDeletedLine($dataOne[$key])];
        } elseif (!array_key_exists($key, $dataOne)) {
            return [$key => addNewLine($dataTwo[$key])];
        } elseif ($dataOne[$key] === $dataTwo[$key]) {
            return [$key => addSameLine($dataTwo[$key])];
        } elseif (is_array($dataOne[$key]) && is_array($dataTwo[$key])) {
            return [$key => addChangedLine(compare($dataOne[$key], $dataTwo[$key]))];
        } else {
            return [$key => addOldAndNew($dataOne[$key], $dataTwo[$key])];
        }
    }, $sortedKeys);
    return array_merge(...$result);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish')
{
    $firstAbsolutePath = makePathAbsolute($pathToFile1);
    $secondAbsolutePath = makePathAbsolute($pathToFile2);
    $firstFile = parseFile($firstAbsolutePath);
    $secondFile = parseFile($secondAbsolutePath);
    $differencies = compare($firstFile, $secondFile);
    return chooseFormateAndPrint($format, $differencies);
}
