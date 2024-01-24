<?php

namespace Differ\Formatters\json;

use Differ\Formatters\Stylish;

// function showJson($array)
// {
//     $result = array_map(function ($value) {
//         ['symbol' => $symbol, 'value' => $difference] = Stylish\getValueAndSymbol($value);
//         switch ($symbol) {
//             case 'both':
//                 $bothValues = [$difference['-'], $difference['+']];
//                 return [...$bothValues];
//             case '+/-':
//                 return showJson($difference);
//         }
//     }, $array);
// }
