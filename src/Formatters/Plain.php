<?php

namespace Differ\Formatters\plain;

use function Differ\Differ\getValueAndSymbol;

function stringifyNullProperly(string $value)
{
    $newValue = $value === "NULL" ? 'null' : $value;
    return $newValue;
}

function showPlain(array $comparedArray, array $tempForKeys = [])
{
    $differencies = array_map(function ($key, $value) use ($tempForKeys) {
        $newKeys = array_merge($tempForKeys, [$key]);
        $keyToPrint = implode('.', $newKeys);
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        $valueString = is_array($difference) ? '[complex value]' : var_export($difference, true);
        $valueToPrint = stringifyNullProperly($valueString);
        switch ($symbol) {
            case 'both':
                $oldValueString = is_array($difference['-'])
                ? '[complex value]'
                : var_export($difference['-'], true);
                $oldValueToPrint = stringifyNullProperly($oldValueString);
                $newValueString = is_array($difference['+'])
                ? '[complex value]'
                : var_export($difference['+'], true);
                $newValueToPrint = stringifyNullProperly($newValueString);
                return "Property '{$keyToPrint}' was updated. From {$oldValueToPrint} to {$newValueToPrint}";
            case '+/-':
                return showPlain($difference, $newKeys);
            case ' ':
                break;
            case '+':
                return "Property '{$keyToPrint}' was added with value: {$valueToPrint}";
            case '-':
                return "Property '{$keyToPrint}' was removed";
            default:
                throw new \Exception("Unknown symbol of value: \"{$symbol}\"!");
        }
    }, array_keys($comparedArray), $comparedArray);
    $withoutEmpty = array_filter($differencies, fn ($array) => $array);
    $result = implode("\n", $withoutEmpty);
    return $result;
}
