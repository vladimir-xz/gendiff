<?php

namespace Differ\Formatters\plain;

use function Differ\Differ\getValueAndSymbol;

function showPlain(array $comparedArray, array $tempForKeys = [])
{
    $result = array_map(function ($key, $value) use ($tempForKeys) {
        $newKeys = array_merge($tempForKeys, [$key]);
        $keyToPrint = implode('.', $newKeys);
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        $valueToPrint = is_array($difference) ? '[complex value]' : var_export($difference, true);
        $valueToPrint === 'NULL' ? 'null' : $valueToPrint;
        switch ($symbol) {
            case 'both':
                $oldValue = is_array($difference['-'])
                ? '[complex value]'
                : var_export($difference['-'], true);
                if ($oldValue === "'null'") {
                    $oldValue = 'null';
                }
                $newValue = is_array($difference['+'])
                ? '[complex value]'
                : var_export($difference['+'], true);
                if ($newValue === "'null'") {
                    $newValue = 'null';
                }
                return "Property '{$keyToPrint}' was updated. From {$oldValue} to {$newValue}";
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
    $withoutEmpty = array_filter($result, fn ($array) => $array);
    $result = implode("\n", $withoutEmpty);
    return $result;
}
