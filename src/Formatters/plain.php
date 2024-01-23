<?php

namespace Differ\Formatters\plain;

use Differ\Formatters\Stylish;

use function Differ\Stylish\makeString;

function showPlain($array, $tempForKeys = [])
{
    $result = array_map(function ($key, $value) use ($tempForKeys) {
        $tempForKeys[] = $key;
        $keyToPrint = implode('.', $tempForKeys);
        $valueToPrint = is_array($value) ? '[complex value]' : trim(json_encode($value, JSON_PRETTY_PRINT), "\"");
        ['symbol' => $symbol, 'value' => $difference] = Stylish\getValueAndSymbol($value);
        switch ($symbol) {
            case 'both':
                $oldValue = is_array($difference['-'])
                ? '[complex value]'
                : trim(json_encode($difference['-'], JSON_PRETTY_PRINT), "\"");
                $newValue = is_array($difference['+'])
                ? '[complex value]'
                : trim(json_encode($difference['+'], JSON_PRETTY_PRINT), "\"");
                return "Property '{$keyToPrint}' was updated. From '{$oldValue}' to '{$newValue}'";
            case '+/-':
                return showPlain($difference, $tempForKeys);
            // case '  ':
            //     return "Property '{$keyToPrint}' stayed the same";
            case '+':
                return "Property '{$keyToPrint}' was added with value: '{$valueToPrint}'";
            case '-':
                return "Property '{$keyToPrint}' was removed";
        }
    }, array_keys($array), $array);
    $withoutEmpty = array_filter($result, fn ($array) => $array);
    return implode("\n", $withoutEmpty);
}

// function showPlain($array, $tempForKeys = [], $acc = '')
// {
//     $last = function ($array) {
//          $name = end($array);
//          return "+ {$name}";
//     };
//     $result = array_map(function ($key, $value) use ($tempForKeys, $acc) {
//         $tempForKeys[] = substr($key, 2);
//         $valueToPrint = is_array($value) ? '[complex value]' : makeString($value);
//         switch ($key[0]) {
//             case '-':
//                 if (array_key_exists($last, $array)) {
//                 }
//                 $acc .= "Property '{$tempForKeys}' was removed";
//                 return;
//             case '+':
//                 $acc .= "Property '{$tempForKeys}' was added with value: {$valueToPrint}\n";
//                 return;
//             case ' ':
//                 $acc = is_array($value) : showPlain($value, )

//         }
//     }, array_keys($array), $array);
// }
