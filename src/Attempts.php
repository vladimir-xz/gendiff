<?php

$hi = [
    "timeout" => 20,
    "verbose" => true,
    "host" => "hexlet.io",
    "follow" => [
        'ty' => 'da',
        'ono' => 'nikogda',
        'kdo' => 'ja'
    ],
    "settings6" => [
        "key" => "value",
        "doge" => [
            "wow" => ""
        ]
    ],
    "array" => [
        'hello',
        'ty kto'
    ]
  ];

$ku = [
    "host" => "hexlet.io",
    "timeout" => 50,
    "proxy" => "123.234.53.22",
    "follow" => [
        'ty' => 'da',
        'oni' => 'net',
        'kdo' => 'on'
    ],
    "settings5" => [
        "key5" => "value5"
    ],
    "settings6" => [
        "key" => "value",
        "ops" => "vops",
        "doge" => [
            "wow" => "so much"
        ]
    ],
    "array" => [
        'one' => 'odin',
        'two' => 'dva'
    ]
];

function ifBothArraysAreOfSameType($array1, $array2)
{
    if (array_key_exists(0, $array1) && !array_key_exists(0, $array2)) {
        return false;
    } elseif (!array_key_exists(0, $array1) && array_key_exists(0, $array2)) {
        return false;
    }
    return true;
}

function lets($array1, $array2)
{
    $merged = array_merge($array1, $array2);
    if (!ifBothArraysAreOfSameType($array1, $array2)) {
        return $merged;
    }
    ksort($merged);
    $result = array_reduce(array_keys($merged), function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            $acc["- {$key}"] = $array1[$key];
            return $acc;
        } elseif (!array_key_exists($key, $array1)) {
            $acc["+ {$key}"] = $array2[$key];
            return $acc;
        } elseif ($array1[$key] === $array2[$key]) {
            $acc["  {$key}"] = $array1[$key];
            return $acc;
        } else {
            if (is_array($array1[$key])) {
                $acc["  {$key}"] = lets($array1[$key], $array2[$key]);
                return $acc;
            } else {
                $acc["- {$key}"] = $array2[$key];
                $acc["+ {$key}"] = $array1[$key];
                return $acc;
            }
        }
    }, []);
    return $result;
}

// $myarray = lets($hi, $ku);
// var_dump($myarray);

$data = [
    'hello' => 'world',
    'is' => true,
    'nested' => 'no',
];


function trying($array1, $array2)
{
    $merged = array_merge($array2, $array1);
    ksort($merged);
    $result = array_map(function ($key, $value) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            return ["+ {$key}" => $value];
        } elseif (!array_key_exists($key, $array1)) {
            return ["- {$key}" => $value];
        } elseif ($array1[$key] === $array2[$key]) {
            return ["{$key}" => $value];
        } else {
            if (is_array($value)) {
                return trying($array1[$key], $array2[$key]);
            } else {
                return [["- {$key}" => $array1[$key]], ["+ {$key}" => $array2[$key]]];
            }
        }
    }, array_keys($merged), $merged);
    return $result;
}

// $myarray = trying($hi, $ku);
// var_dump($myarray);

function showing($array)
{
    $result = array_map(function ($item) {
        if (is_array($item)) {
            return showing($item);
        }
        return $item;
    }, $array);
    $toJson = json_encode($result);
    return "\n{$toJson}\n";
}

echo (showing($data));
