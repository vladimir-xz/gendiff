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
        ['status' => $status, 'difference' => $difference] = getNode($node);
        $key = key($difference);
        $value = current($difference);
        $newKeys = array_merge($tempForKeys, [$key]);
        $keyToPrint = implode('.', $newKeys);
        switch ($status) {
            case 'old and new':
                $oldAndNewValues = array_map(function ($node) {
                    ['status' => $status, 'difference' => $difference] = getNode($node);
                        $valueToPrint = printValuePlain(current($difference));
                        return [$status => $valueToPrint];
                }, $value);
                $bothValues = array_merge(...$oldAndNewValues);
                return "Property '{$keyToPrint}' was updated. From {$bothValues['deleted']} to {$bothValues['added']}";
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
                throw new \Exception("Unknown status of value: \"{$status}\"!");
        }
    }, $comparedArray);
    $withoutEmpties = array_filter($differencies, fn ($array) => $array);
    return implode("\n", $withoutEmpties);
}
