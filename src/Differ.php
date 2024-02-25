<?php

namespace Differ\Differ;

use Differ\FilesProcessing;

use function Differ\Parsers\parseFile;
use function Differ\Differ\chooseFormateAndPrint;
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

function getNode(mixed $array)
{
    return [
        'status' => $array['status'] ?? 'unsorted',
        'symbol' => $array['symbol'] ?? '  ',
        'difference' => $array['difference'] ?? $array
    ];
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
    $firstAbsolutePath = FilesProcessing\makePathAbsolute($pathToFile1);
    $secondAbsolutePath = FilesProcessing\makePathAbsolute($pathToFile2);
    $firstContent = FilesProcessing\getFilesContent($firstAbsolutePath);
    $secondContent = FilesProcessing\getFilesContent($secondAbsolutePath);
    $firstFile = parseFile(pathinfo($firstAbsolutePath, PATHINFO_EXTENSION), $firstContent);
    $secondFile = parseFile(pathinfo($secondAbsolutePath, PATHINFO_EXTENSION), $secondContent);
    $differencies = compare($firstFile, $secondFile);
    return chooseFormateAndPrint($format, $differencies);
}
