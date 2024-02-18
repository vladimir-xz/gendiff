<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\getNode;

function printJson(array $comparedData): string
{
    $result = array_map(function ($data) {
        ['status' => $status, 'symbol' => $symbol, 'difference' => $difference] = getNode($data);
        $key = key($difference);
        $value = current($difference);
        if ($status === 'old and new') {
            $oldAndNewValues = array_map(function ($node) use ($key) {
                $stringValue = json_encode(current($node['difference']), 0, 512);
                return "\"{$node['symbol']}{$key}\":{$stringValue}";
            }, $value);
            return implode(',', $oldAndNewValues);
        } elseif ($status === 'changed') {
            $innerJson = printJson($value);
            return "\"{$key}\":{$innerJson}";
        } else {
            $valueJson = json_encode($value);
            return "\"{$symbol}{$key}\":{$valueJson}";
        }
    }, $comparedData);
    $keysAndValuesInString = implode(',', $result);
    return "{{$keysAndValuesInString}}";
}
