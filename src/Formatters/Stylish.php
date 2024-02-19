<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\getNode;
use function Differ\Differ\getKeyAndValue;

function stringify(mixed $item)
{
    $jsonFile = json_encode($item, JSON_PRETTY_PRINT, 512);
    if ($jsonFile === false) {
        throw new \Exception('Error when turning value into string');
    }
    $result = trim($jsonFile, "\"");
    return $result;
}

function makeStylish2(mixed $comparedData, int $depth = 0, string $separator = '    ', int $offset = 2)
{
    if (!is_array($comparedData)) {
        return stringify($comparedData);
    }
    $emptySpace = str_repeat($separator, $depth);
    $result = array_map(function ($key, $data) use ($separator, $depth, $offset) {
        $nextDepth =  $depth + 1;
        $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
        ['status' => $status, 'symbol' => $symbol, 'difference' => $difference] = getNode($data);
        if ($status === 'unsorted') {
            $keyOfValue = $key;
            $value = $data;
        } else {
            $keyOfValue = key($difference);
            $value = current($difference);
        }
        if ($status === 'old and new') {
            $oldAndNewValues = array_map(function ($node) use ($nextDepth, $emptySpace) {
                ['symbol' => $symbol, 'difference' => $difference] = getNode($node);
                $key = key($difference);
                $value = current($difference);
                $stringValue = makeStylish($value, $nextDepth);
                return "{$emptySpace}{$symbol}{$key}: {$stringValue}";
            }, $value);
            return implode("\n", $oldAndNewValues);
        } else {
            $convertedValue = makeStylish($value, $nextDepth);
        }
        return "{$emptySpace}{$symbol}{$keyOfValue}: {$convertedValue}";
    }, array_keys($comparedData), $comparedData);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$emptySpace}}";
}

function makeStylish(mixed $comparedData, int $depth = 0, string $separator = '    ', int $offset = 2)
{
    if (!is_array($comparedData)) {
        return stringify($comparedData);
    }
    $emptySpace = str_repeat($separator, $depth);
    $iter = function ($comparedData) use (&$iter, $depth, $separator, $offset) {
        $result = array_map(function ($key, $data) use ($iter, $depth, $separator, $offset) {
            $nextDepth =  $depth + 1;
            $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
            ['status' => $status, 'symbol' => $symbol, 'difference' => $difference] = getNode($data);
            if ($status === 'unsorted') {
                $keyOfValue = $key;
                $value = $data;
            } else {
                $keyOfValue = key($difference);
                $value = current($difference);
            }
            if ($status === 'old and new') {
                return implode("\n", $iter($value));
            } else {
                $convertedValue = makeStylish($value, $nextDepth);
            }
            return "{$emptySpace}{$symbol}{$keyOfValue}: {$convertedValue}";
        }, array_keys($comparedData), $comparedData);
        return $result;
    };
    $final = implode("\n", $iter($comparedData));
    return "{\n{$final}\n{$emptySpace}}";
}

// $valueString = makeStylish($value, $nextDepth);
// return "{$emptySpace}{$key}: {$valueString}";
