<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\getValueAndSymbol;

function printJson(array $comparedData): string
{
    $result = array_map(function ($key, $value) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        if ($symbol === 'both') {
            $deletedValue = json_encode($difference['-'], 0, 512);
            $addedValue = json_encode($difference['+'], 0, 512);
            return ["\"- {$key}\":{$deletedValue},\"+ {$key}\":{$addedValue}"];
        } elseif ($symbol === '+/-') {
            $innerJson = printJson($difference);
            return ["\"{$key}\":{$innerJson}"];
        } else {
            $valueJson = json_encode($difference);
            return ["\"{$symbol} {$key}\":{$valueJson}"];
        }
    }, array_keys($comparedData), $comparedData);
    $keysAndValuesInString = implode(',', array_merge(...$result));
    return "{{$keysAndValuesInString}}";
}
