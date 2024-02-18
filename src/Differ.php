<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFile;
use function Differ\Parsers\makePathAbsolute;
use function Differ\Formatters\chooseFormateAndPrint;
use function Functional\sort;

function addNewLine(mixed $array)
{
    return ['status' => 'added', 'symbol' => '+ ', 'difference' => $array ];
}

function addDeletedLine(mixed $array)
{
    return ['status' => 'deleted', 'symbol' => '- ', 'difference' => $array ];
}

function addSameLine(mixed $array)
{
    return ['status' => 'same', 'symbol' => '  ', 'difference' => $array ];
}

function addChangedLine(mixed $array)
{
    return ['status' => 'changed', 'symbol' => '  ', 'difference' => $array ];
}

function addOldAndNew(mixed $old, mixed $new)
{
    $commomKey = key($old);
    return ['status' => 'old and new',
    'symbol' => '',
     'difference' => [$commomKey => [addDeletedLine($old), addNewLine($new)]]];
}

function getNode(array $array)
{
    return [
        'status' => $array['status'] ?? 'unsorted',
        'symbol' => $array['symbol'] ?? '  ',
        'difference' => $array['difference'] ?? $array
    ];
}

function getKeyAndValue(array $difference)
{
    return ['key' => key($difference),'value' => current($difference)];
}

function compare(array $dataOne, array $dataTwo)
{
    $mergedKeys = array_merge(array_keys($dataOne), array_keys($dataTwo));
    $sortedKeys = sort(array_unique($mergedKeys), fn ($left, $right) => strcmp($left, $right), true);
    $result = array_map(function ($key) use ($dataOne, $dataTwo) {
        if (!array_key_exists($key, $dataTwo)) {
            return addDeletedLine([$key => $dataOne[$key]]);
        } elseif (!array_key_exists($key, $dataOne)) {
            return addNewLine([$key => $dataTwo[$key]]);
        } elseif ($dataOne[$key] === $dataTwo[$key]) {
            return addSameLine([$key => $dataTwo[$key]]);
        } elseif (is_array($dataOne[$key]) && is_array($dataTwo[$key])) {
            return addChangedLine([$key => compare($dataOne[$key], $dataTwo[$key])]);
        } else {
            return addOldAndNew([$key => $dataOne[$key]], [$key => $dataTwo[$key]]);
        }
    }, $sortedKeys);
    return $result;
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
