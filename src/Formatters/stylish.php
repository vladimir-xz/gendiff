<?php

namespace Differ\Formatters\Stylish;

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

function getValueAndSymbol($array)
{
    return ['symbol' => $array['symbol'], 'value' => $array['value']];
}

// function getNewLine($array)
// {
//     if (array_key_exists('+', $array)) {
//         return ['symbol' => '+ ', 'value' => $array['+']];
//     }
//     return null;
// }

// function getDeletedLine($array)
// {
//     if (array_key_exists('-', $array)) {
//         return ['symbol' => '- ', 'value' => $array['-']];
//     }
//     return null;
// }

// function getSameLine($array)
// {
//     if (array_key_exists('same', $array)) {
//         return ['symbol' => '  ', 'value' => $array['same']];
//     }
//     return null;
// }

// function getChangedLine($array)
// {
//     if (array_key_exists('+/-', $array)) {
//         return ['symbol' => '+/-', 'value' => $array['+/-']];
//     }
//     return null;
// }

// function getBothLines($array)
// {
//     if (array_key_exists('-', $array) && array_key_exists('+', $array)) {
//         return ['symbol' => 'both', 'value' => ['-' => $array['-'], '+' => $array['+']]];
//     }
//     return null;
// }

// function findandGetDifference($value)
// {
//     if (!is_null(getBothLines($value))) {
//         return getBothLines($value);
//     } elseif (!is_null(getSameLine($value))) {
//         return getSameLine($value);
//     } elseif (!is_null(getDeletedLine($value))) {
//         return getDeletedLine($value);
//     } elseif (!is_null(getNewLine($value))) {
//         return getNewLine($value);
//     } elseif (!is_null(getChangedLine($value))) {
//         return getChangedLine($value);
//     }
// }

function showStylish($array, $separator = '    ', $depth = 0, $offset = 2)
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $offset) {
        $depth += 1;
        $adding = substr(str_repeat($separator, $depth), $offset);
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        if ($symbol === 'both') {
            $deletedValue = toString($difference['-'], $separator, $depth);
            $addedValue = toString($difference['+'], $separator, $depth);
            $line = "{$adding}- {$key}: {$deletedValue}\n{$adding}+ {$key}: {$addedValue}";
            return $line;
        } elseif ($symbol === '+/-') {
            $symbol = ' ';
            $newValue = showStylish($difference, $separator, $depth, $offset);
        } else {
            $newValue = toString($difference, $separator, $depth);
        }
        $line = "{$adding}{$symbol} {$key}: {$newValue}";
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
