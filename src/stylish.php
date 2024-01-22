<?php

namespace Differ\Stylish;

function makeString(mixed $item)
{
    if (!is_array($item)) {
        $result = trim(json_encode($item, JSON_PRETTY_PRINT), "\"");
        return $result;
    }
    return $item;
}

function toString(mixed $item, $separator = '    ', $depth = 0)
{
    $adding = str_repeat($separator, $depth);
    if (!is_array($item)) {
        return trim(var_export($item, true), "'");
    }
    $result = array_map(function ($key, $value) use ($separator, $depth) {
        $depth += 1;
        $adding = str_repeat($separator, $depth);
        $stringValue = toString($value, $separator, $depth);
        $result = "{$adding}{$key}: {$stringValue}";
        return $result;
    }, array_keys($item), $item);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}

function getNewLine($array)
{
    if (array_key_exists('+', $array)) {
        return ['symbol' => '+ ', 'value' => $array['+']];
    }
    return null;
}

function getDeletedLine($array)
{
    if (array_key_exists('-', $array)) {
        return ['symbol' => '- ', 'value' => $array['-']];
    }
    return null;
}

function getSameLine($array)
{
    if (array_key_exists('same', $array)) {
        return ['symbol' => '  ', 'value' => $array['same']];
    }
    return null;
}

function getChangedLine($array)
{
    if (array_key_exists('+/-', $array)) {
        return ['symbol' => '  ', 'value' => $array['+/-']];
    }
    return null;
}

function showPrettyAssociative($array, $separator = '    ', $depth = 0, $offset = 2)
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        $depth += 1;
        $adding = substr(str_repeat($separator, $depth), $offset);
        if (!is_null(getDeletedLine($value)) && !is_null(getNewLine($value))) {
            ['symbol' => $symbolMinus, 'value' => $deletedValue] = getDeletedLine($value);
            $deletedValue = toString($deletedValue, $separator, $depth, $offset);
            ['symbol' => $symbolPlus, 'value' => $addedValue] = getNewLine($value);
            $addedValue = toString($addedValue, $separator, $depth, $offset);
            $line = "{$adding}{$symbolMinus}{$key}: {$deletedValue}\n{$adding}{$symbolPlus}{$key}: {$addedValue}";
            return $line;
        } elseif (!is_null(getSameLine($value))) {
            ['symbol' => $symbol, 'value' => $newValue] = getSameLine($value);
        } elseif (!is_null(getDeletedLine($value))) {
            ['symbol' => $symbol, 'value' => $newValue] = getDeletedLine($value);
        } elseif (!is_null(getNewLine($value))) {
            ['symbol' => $symbol, 'value' => $newValue] = getNewLine($value);
        } elseif (!is_null(getChangedLine($value))) {
            ['symbol' => $symbol, 'value' => $newValue] = getChangedLine($value);
            $newValue = showPrettyAssociative($newValue, $separator, $depth, $offset);
        }
        $newValue = toString($newValue, $separator, $depth, $offset);
        $line = "{$adding}{$symbol}{$key}: {$newValue}";
        return $line;
    }, array_keys($array), $array);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}

function printing($array, $separator = '    ', $depth = 0, $offset = 2)
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        $depth += 1;
        if (in_array($key[0], ['+', '-', ' '])) {
            $adding = str_repeat($separator, $depth);
            $adding = substr($adding, $offset);
        } else {
            $adding = str_repeat($separator, $depth);
        }
        $result = "{$adding}{$key}";
        if (is_array($value)) {
            $convertedValue = printing($value, $separator, $depth, $offset);
        } else {
            $convertedValue = makeString($value);
        }
        $result .= ": {$convertedValue}";
        return $result;
    }, array_keys($array), $array);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}
