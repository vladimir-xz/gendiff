<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\getNod;

function printJson(array $comparedData): string
{
    $result = array_map(function ($key, $value) {
        ['status' => $status, 'symbol' => $symbol, 'value' => $difference] = getNod($value);
        if ($status === 'old and new') {
            $deletedValue = json_encode($difference['-'], 0, 512);
            $addedValue = json_encode($difference['+'], 0, 512);
            return ["\"- {$key}\":{$deletedValue},\"+ {$key}\":{$addedValue}"];
        } elseif ($status === 'changed') {
            $innerJson = printJson($difference);
            return ["\"{$key}\":{$innerJson}"];
        } else {
            $valueJson = json_encode($difference);
            return ["\"{$symbol}{$key}\":{$valueJson}"];
        }
    }, array_keys($comparedData), $comparedData);
    $keysAndValuesInString = implode(',', array_merge(...$result));
    return "{{$keysAndValuesInString}}";
}
