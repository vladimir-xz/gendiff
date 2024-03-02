<?php

namespace Differ\Formatters\Plain;

use function Differ\Differ\getNode;

function printValuePlain(mixed $value): string
{
    $valueString = is_array($value)
    ? '[complex value]'
    : var_export($value, true);
    return $valueString === "NULL" ? 'null' : $valueString;
}

function format(array $comparedArray, array $tempForKeys = []): string
{
    $differencies = array_map(function ($node) use ($tempForKeys) {
        ['type' => $type, 'key' => $key, 'value' => $value] = getNode($node);
        $newKeys = array_merge($tempForKeys, [$key]);
        $keyToPrint = implode('.', $newKeys);
        switch ($type) {
            case 'old and new':
                $oldValue = printValuePlain($value['oldValue']);
                $newValue = printValuePlain($value['newValue']);
                return "Property '{$keyToPrint}' was updated. From {$oldValue} to {$newValue}";
            case 'changed':
                return format($value, $newKeys);
            case 'same':
                break;
            case 'added':
                $valueString = printValuePlain($value);
                return "Property '{$keyToPrint}' was added with value: {$valueString}";
            case 'deleted':
                return "Property '{$keyToPrint}' was removed";
            default:
                throw new \Exception("Unknown type of value: \"{$type}\"!");
        }
    }, $comparedArray);
    $withoutEmpties = array_filter($differencies, fn ($array) => $array);
    return implode("\n", $withoutEmpties);
}
