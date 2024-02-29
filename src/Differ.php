<?php

namespace Differ\Differ;

use Differ\FileProcessing;

use function Differ\Parsers\parseFile;
use function Differ\Formatters\chooseFormateAndPrint;
use function Functional\sort;

function addNewLine(mixed $value): array
{
    return ['type' => 'added', 'key' => key($value), 'difference' => $value ];
}

function addDeletedLine(mixed $value): array
{
    return ['type' => 'deleted', 'key' => key($value), 'difference' => $value ];
}

function addSameLine(mixed $value): array
{
    return ['type' => 'same', 'key' => key($value), 'difference' => $value ];
}

function addChangedLine(mixed $value): array
{
    return ['type' => 'changed', 'key' => key($value), 'difference' => $value ];
}

function addOldAndNew(string $commonKey, mixed $old, mixed $new): array
{
    return ['type' => 'old and new',
    'key' => $commonKey,
    'difference' => [addDeletedLine([$commonKey => $old]), addNewLine([$commonKey => $new])]];
}

function getNode(mixed $value): array
{
    return [
        'type' => $value['type'],
        'key' => $value['key'],
        'difference' => $value['difference']
    ];
}

function compare(array $dataOne, array $dataTwo): array
{
    $mergedKeys = array_merge(array_keys($dataOne), array_keys($dataTwo));
    $sortedKeys = sort(array_unique($mergedKeys), fn ($left, $right) => strcmp($left, $right), false);
    return array_map(function ($key) use ($dataOne, $dataTwo) {
        if (!array_key_exists($key, $dataTwo)) {
            return addDeletedLine([$key => $dataOne[$key]]);
        } elseif (!array_key_exists($key, $dataOne)) {
            return addNewLine([$key => $dataTwo[$key]]);
        } elseif ($dataOne[$key] === $dataTwo[$key]) {
            return addSameLine([$key => $dataTwo[$key]]);
        } elseif (is_array($dataOne[$key]) && is_array($dataTwo[$key])) {
            return addChangedLine([$key => compare($dataOne[$key], $dataTwo[$key])]);
        } else {
            return addOldAndNew($key, $dataOne[$key], $dataTwo[$key]);
        }
    }, $sortedKeys);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $firstAbsolutePath = FileProcessing\makePathAbsolute($pathToFile1);
    $secondAbsolutePath = FileProcessing\makePathAbsolute($pathToFile2);
    $firstContent = FileProcessing\getFilesContent($firstAbsolutePath);
    $secondContent = FileProcessing\getFilesContent($secondAbsolutePath);
    $firstFile = parseFile(pathinfo($firstAbsolutePath, PATHINFO_EXTENSION), $firstContent);
    $secondFile = parseFile(pathinfo($secondAbsolutePath, PATHINFO_EXTENSION), $secondContent);
    $differencies = compare($firstFile, $secondFile);
    return chooseFormateAndPrint($format, $differencies);
}
