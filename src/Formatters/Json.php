<?php

namespace Differ\Differ;

use function Differ\Differ\getNode;

function makeArrayForPrint(array $comparedData): array
{
    $result = array_map(function ($data) {
        ['status' => $status, 'difference' => $difference] = getNode($data);
        $key = key($difference);
        $value = current($difference);
        if ($status === 'old and new') {
            $oldAndNewValues = array_map(function ($node) use ($key) {
                ['symbol' => $symbol, 'difference' => $difference] = getNode($node);
                return ["{$symbol}{$key}" => current($difference)];
            }, $value);
            return array_merge(...$oldAndNewValues);
        } elseif ($status === 'changed') {
            return [$key => makeArrayForPrint($value)];
        } else {
            return $difference;
        }
    }, $comparedData);
    return array_merge(...$result);
}

function printJson(array $comparedArray)
{
    $preparedArray = makeArrayForPrint($comparedArray);
    return json_encode($preparedArray);
}
