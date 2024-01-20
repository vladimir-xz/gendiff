<?php

require_once __DIR__ . '/../vendor/autoload.php';

use function Functional\sort;

$nestedFirstFileJson = __DIR__ . "/../tests/fixtures/NestedOne.json";
$nestedSecondFileJson = __DIR__ . "/../tests/fixtures/NestedTwo.json";
$contentOne = file_get_contents($nestedFirstFileJson, true);
$contentOne = json_decode($contentOne, true);
$contentTwo = file_get_contents($nestedSecondFileJson, true);
$contentTwo = json_decode($contentTwo, true);

$jsonTwo = [
    "timeout" => 20,
    "verbose" => 'hello',
    "host" => "hexlet.io",
    "settings6" => [
        "key" => true,
        "doge" => [
            "wow" => ""
        ]
    ]
];

$jsonOne = [
    "host" => "hexlet.io",
    "timeout" => 50,
    "proxy" => "123.234.53.22",
     "follow" => "haha"
];

$hi = [
    "settings6" => [
        "key" => true,
        "doge" => [
            "wow" => ""
        ]
    ],
  ];

$ku = [
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
    "group3" => [
        "deep" => [
          "id" => [
            "number" => 45
          ]
        ],
        "fee" => 100500
      ]
];

function ifArraysOfSameType($array1, $array2)
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
    if (!ifArraysOfSameType($array1, $array2)) {
        return $merged;
    }
    ksort($merged);
    $result = array_reduce(array_keys($merged), function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            $acc["- {$key}"] = ($array1[$key]);
            return $acc;
        } elseif (!array_key_exists($key, $array1)) {
            $acc["+ {$key}"] = ($array2[$key]);
            return $acc;
        } elseif ($array1[$key] === $array2[$key]) {
            $acc["  {$key}"] = ($array1[$key]);
            return $acc;
        } else {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                $acc["  {$key}"] = lets($array1[$key], $array2[$key]);
                return $acc;
            } else {
                $acc["- {$key}"] = ($array1[$key]);
                $acc["+ {$key}"] = ($array2[$key]);
                return $acc;
            }
        }
    }, []);
    return $result;
}

function printing($array, $separator = '*', $depth = 0, $adding = '')
{
    $adding = str_repeat($separator, $depth);
    $result = array_map(function ($key, $value) use ($separator, $depth, $adding) {
        $depth += 4;
        if (in_array($key[0], ['+', '-', ' '])) {
            $adding = str_repeat($separator, $depth - 2);
        } else {
            $adding = str_repeat($separator, $depth);
        }
        $result = "{$adding}{$key}";
        if (is_array($value)) {
            $value = printing($value, $separator, $depth, $adding);
        }
        $result .= ": {$value}";
        return $result;
    }, array_keys($array), $array);
    $final = implode("\n", $result);
    return "{\n{$final}\n{$adding}}";
}

$myarray = lets($contentOne, $contentTwo);
echo printing($myarray);

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
    $toJson = json_encode($result, JSON_PRETTY_PRINT);
    echo "{$toJson}";
    return "{$toJson}";
}


function associative($array1, $array2)
{
    $merged = array_merge($array1, $array2);
    if (!ifArraysOfSameType($array1, $array2)) {
        return $merged;
    }
    //sort($merged, fn ($left, $right) => strcmp($left, $right));
    $result = array_reduce(array_keys($merged), function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            $acc[$key]['deleted'] = $array1[$key];
            return $acc;
        } elseif (!array_key_exists($key, $array1)) {
            $acc[$key]['new'] = $array2[$key];
            return $acc;
        } elseif ($array1[$key] === $array2[$key]) {
            $acc[$key]['same'] = $array1[$key];
            return $acc;
        } else {
            if (is_array($array1[$key])) {
                $acc[$key] = associative($array1[$key], $array2[$key]);
                return $acc;
            } else {
                $acc[$key]['deleted'] = $array2[$key];
                $acc[$key]['new'] = $array1[$key];
                return $acc;
            }
        }
    }, []);
    return $result;
}

function getDeleted($array)
{
    return $array['deleted'] ?? null;
}

function getNew($array)
{
    return $array['new'] ?? null;
}

function getSame($array)
{
    return $array['same'] ?? null;
}

function showSmoker($array)
{
    $result = array_map(function ($key, $value) {
        $new = getNew($value);
        $old = getDeleted($value);
        $same = getSame($value);
        $result = [];
        if ($old) {
            $result[] = "- {$key}: {$old}";
        }
        if ($new) {
            $result[] = "+ {$key}: {$new}\n";
        }
        if ($same) {
            $result[] = "  {$key}: {$same}\n";
        }
        $result = implode("\n", $result);
        echo $result;
        return $result;
    }, array_keys($array), $array);
    $result = implode("\n", $result);
    return $result;
}
