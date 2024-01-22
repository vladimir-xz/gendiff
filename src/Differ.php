<?php

namespace Differ\Differ;

use Differ\Stylish;

use function Differ\Parsers\parseFile;
use function Differ\Stylish\showPrettyAssociative;

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

$jsonOne = [
"host" => "hexlet.io",
"timeout" => 50,
"proxy" => "123.234.53.22",
"follow" => false
];

$jsonTwo = [
    "timeout" => 20,
    "verbose" => false,
    "host" => "hexlet.io"
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

function addNewLine($array)
{
    return ['+' => $array];
}

function addDeletedLine($array)
{
    return ['-' => $array];
}

function addSameLine($array)
{
    return ['same' => $array];
}

function addChangedLine($array)
{
    return ['+/-' => $array];
}

function addOldAndNew($old, $new)
{
    return ['-' => $old, '+' => $new];
}

function compareAssociative($array1, $array2)
{
    $merged = array_merge($array1, $array2);
    ksort($merged);
    $result = array_reduce(array_keys($merged), function ($acc, $key) use ($array1, $array2) {
        if (!array_key_exists($key, $array2)) {
            $acc[$key] = addDeletedLine($array1[$key]);
            return $acc;
        } elseif (!array_key_exists($key, $array1)) {
            $acc[$key] = addNewLine($array2[$key]);
            return $acc;
        } elseif ($array1[$key] === $array2[$key]) {
            $acc[$key] = addSameLine($array1[$key]);
            return $acc;
        } else {
            if (is_array($array1[$key]) && is_array($array2[$key])) {
                $acc[$key] = addChangedLine(compareAssociative($array1[$key], $array2[$key]));
                return $acc;
            } else {
                $acc[$key] = addOldAndNew($array1[$key], $array2[$key]);
                return $acc;
            }
        }
    }, []);
    return $result;
}

// $one = compareAssociative($jsonOne, $jsonTwo);
// // $rrr = Stylish\showPrettyAssociative($one);
// echo $rrr;

function compareArrays($array1, $array2)
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
                $acc["  {$key}"] = compareArrays($array1[$key], $array2[$key]);
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

function genDiff($pathToFile1, $pathToFile2, $format)
{
    $firstFile = (array)parseFile($pathToFile1);
    $secondFile = (array)parseFile($pathToFile2);
    $array = compareAssociative($firstFile, $secondFile);
    switch ($format) {
        case 'stylish':
            return (showPrettyAssociative($array));
    }
}
