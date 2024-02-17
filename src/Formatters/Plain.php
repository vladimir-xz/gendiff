<?php

namespace Differ\Formatters\Plain;

use function Differ\Differ\getNod;

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
    $differencies = array_map(function ($key, $value) use ($tempForKeys) {
        $newKeys = array_merge($tempForKeys, [$key]);
        $keyToPrint = implode('.', $newKeys);
        ['status' => $status, 'value' => $difference] = getNod($value);
        switch ($status) {
            case 'old and new':
                $oldAndNewValues = array_map(fn ($value) => printValuePlain($value['value']), $difference);
                // $oldValueString = is_array($difference['-'])
                // ? '[complex value]'
                // : var_export($difference['-'], true);
                // $oldValueToPrint = stringifyNullProperly($oldValueString);
                // $newValueString = is_array($difference['+'])
                // ? '[complex value]'
                // : var_export($difference['+'], true);
                // $newValueToPrint = stringifyNullProperly($newValueString);
                return "Property '{$keyToPrint}' was updated. From {$oldAndNewValues[0]} to {$oldAndNewValues[1]}";
            case 'changed':
                return showPlain($difference, $newKeys);
            case 'same':
                break;
            case 'added':
                $valueString = printValuePlain($difference);
                return "Property '{$keyToPrint}' was added with value: {$valueString}";
            case 'deleted':
                return "Property '{$keyToPrint}' was removed";
            default:
                throw new \Exception("Unknown status of value: \"{$status}\"!");
        }
    }, array_keys($comparedArray), $comparedArray);
    $withoutEmpty = array_filter($differencies, fn ($array) => $array);
    $result = implode("\n", $withoutEmpty);
    return $result;
}
