<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\getNode;
use function Differ\Differ\getKeyAndValue;
use function Functional\curry;

function makeString(mixed $item, string $separator = '    ', int $depth = 0)
{
    if (!is_array($item)) {
        $jsonFile = json_encode($item, JSON_PRETTY_PRINT, 512);
        if ($jsonFile === false) {
            throw new \Exception('Error when turning value into string');
        }
        $result = trim($jsonFile, "\"");
        return $result;
    }
    $emptySpace = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth) {
        $nextDepth =  $depth + 1;
        $emptySpace = str_repeat($separator, $nextDepth);
        $resultKey = "{$emptySpace}{$key}";
        $convertedValue = makeString($value, $separator, $nextDepth);
        $result = "{$resultKey}: {$convertedValue}";
        return $result;
    }, array_keys($item), $item);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$emptySpace}}";
}

function makeStringUsingInterfaces(array $comparedData, string $separator = '    ', int $depth = 0, int $offset = 2)
{
    $emptySpace = str_repeat($separator, $depth);
    $result = array_map(function ($node) use ($separator, $depth, $offset) {
        ['status' => $status, 'symbol' => $symbol, 'difference' => $difference] = getNode($node);
        ['key' => $key, 'value' => $value] = getKeyAndValue($difference);
        $nextDepth =  $depth + 1;
        $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
        if ($status === 'old and new') {
            $oldAndNewValues = array_map(function ($node) use ($separator, $nextDepth, $emptySpace) {
                ['symbol' => $symbol, 'difference' => $difference] = getNode($node);
                $key = key($difference);
                $value = current($difference);
                $stringValue = makeString($value, $separator, $nextDepth);
                return "{$emptySpace}{$symbol}{$key}: {$stringValue}";
            }, $value);
            return implode("\n", $oldAndNewValues);
        } elseif ($status === 'changed') {
            $convertedValue = makeStringUsingInterfaces($value, $separator, $nextDepth);
            return "{$emptySpace}{$symbol}{$key}: {$convertedValue}";
        } else {
            $valueString = makeString($value, $separator, $nextDepth);
            return "{$emptySpace}{$symbol}{$key}: {$valueString}";
        }
    }, $comparedData);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$emptySpace}}";
}

function stringify(mixed $item)
{
    $jsonFile = json_encode($item, JSON_PRETTY_PRINT, 512);
    if ($jsonFile === false) {
        throw new \Exception('Error when turning value into string');
    }
    $result = trim($jsonFile, "\"");
    return $result;
}

function makeStylish(mixed $comparedData, int $depth = 0, string $separator = '    ', int $offset = 2)
{
    if (!is_array($comparedData)) {
        return stringify($comparedData);
    }
    $emptySpace = str_repeat($separator, $depth);
    $result = array_map(function ($key, $node) use ($separator, $depth, $offset) {
        ['status' => $status, 'symbol' => $symbol, 'difference' => $difference] = getNode($node);
        if ($status === 'unsorted') {
            $keyOfValue = $key;
            $value = $node;
        } else {
            $keyOfValue = key($difference);
            $value = current($difference);
        }
        $nextDepth =  $depth + 1;
        $emptySpace = substr(str_repeat($separator, $nextDepth), $offset, null);
        if ($status === 'old and new') {
            $oldAndNewValues = array_map(function ($node) use ($nextDepth, $emptySpace) {
                ['symbol' => $symbol, 'difference' => $difference] = getNode($node);
                $key = key($difference);
                $value = current($difference);
                $stringValue = makeStylish($value, $nextDepth, false);
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

// $valueString = makeStylish($value, $nextDepth);
// return "{$emptySpace}{$key}: {$valueString}";
