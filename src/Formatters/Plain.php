<?php

namespace Differ\Differ;

use function Differ\Differ\getNode;

function stringifyNullProperly(string $value)
{
    $newValue = $value === "NULL" ? 'null' : $value;
    return $newValue;
}

function printValuePlain(mixed $value)
{
    $valueString = is_array($value)
    ? '[complex value]'
    : var_export($value, true);
    $valueToPrint = stringifyNullProperly($valueString);
    return $valueToPrint;
}

function showPlain(array $comparedArray, array $tempForKeys = [])
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
                return showPlain($value, $newKeys);
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
    $withoutEmpty = array_filter($differencies, fn ($array) => $array);
    $result = implode("\n", $withoutEmpty);
    return $result;
}
