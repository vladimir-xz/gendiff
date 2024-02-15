<?php

namespace Differ\Formatters\Json;

function getValueAndSymbol($array)
{
    return ['symbol' => $array['symbol'], 'value' => $array['value']];
}

function makeArrayFromDifferencies(array $comparedData)
{
    $result = array_map(function ($key, $value) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        if ($symbol === 'both') {
            $deletedValue = ["- {$key}" => $difference['-']];
            $addedValue = ["+ {$key}" => $difference['+']];
            return [...$deletedValue, ...$addedValue];
        } elseif ($symbol === '+/-') {
            return ["  {$key}" => makeArrayFromDifferencies($difference)];
        } else {
            return ["{$symbol} {$key}" => $difference];
        }
    }, array_keys($comparedData), $comparedData);
    return array_merge(...$result);
}

function makeJson(array $comparedData): string
{
    $result = array_map(function ($key, $value) {
        ['symbol' => $symbol, 'value' => $difference] = getValueAndSymbol($value);
        if ($symbol === 'both') {
            $deletedValue = json_encode($difference['-'], 0, 512);
            $addedValue = json_encode($difference['+'], 0, 512);
            return ["{\"- {$key}\":{$deletedValue},\"+ {$key}\":{$addedValue}}"];
        } elseif ($symbol === '+/-') {
            $innerJson = makeJson($difference);
            return ["{\"{$key}\":{$innerJson}}"];
        } else {
            $valueJson = json_encode($difference);
            return ["{\"{$symbol} {$key}\":{$valueJson}}"];
        }
    }, array_keys($comparedData), $comparedData);
    $arrayWithKeys = array_merge(...$result);
    return implode('', $arrayWithKeys);
}

$comp = array (
    'common' =>
    array (
      'symbol' => '+/-',
      'value' =>
      array (
        'follow' =>
        array (
          'symbol' => '+',
          'value' => false,
        ),
        'setting1' =>
        array (
          'symbol' => ' ',
          'value' => 'Value 1',
        ),
        'setting2' =>
        array (
          'symbol' => '-',
          'value' => 200,
        ),
        'setting3' =>
        array (
          'symbol' => 'both',
          'value' =>
          array (
            '-' => true,
            '+' => 'null',
          ),
        ),
        'setting4' =>
        array (
          'symbol' => '+',
          'value' => 'blah blah',
        ),
        'setting5' =>
        array (
          'symbol' => '+',
          'value' =>
          array (
            'key5' => 'value5',
          ),
        ),
        'setting6' =>
        array (
          'symbol' => '+/-',
          'value' =>
          array (
            'doge' =>
            array (
              'symbol' => '+/-',
              'value' =>
              array (
                'wow' =>
                array (
                  'symbol' => 'both',
                  'value' =>
                  array (
                    '-' => '',
                    '+' => 'so much',
                  ),
                ),
              ),
            ),
            'key' =>
            array (
              'symbol' => ' ',
              'value' => 'value',
            ),
            'ops' =>
            array (
              'symbol' => '+',
              'value' => 'vops',
            ),
          ),
        ),
      ),
    ),
    'group1' =>
    array (
      'symbol' => '+/-',
      'value' =>
      array (
        'baz' =>
        array (
          'symbol' => 'both',
          'value' =>
          array (
            '-' => 'bas',
            '+' => 'bars',
          ),
        ),
        'foo' =>
        array (
          'symbol' => ' ',
          'value' => 'bar',
        ),
        'nest' =>
        array (
          'symbol' => 'both',
          'value' =>
          array (
            '-' =>
            array (
              'key' => 'value',
            ),
            '+' => 'str',
          ),
        ),
      ),
    ),
    'group2' =>
    array (
      'symbol' => '-',
      'value' =>
      array (
        'abc' => 12345,
        'deep' =>
        array (
          'id' => 45,
        ),
      ),
    ),
    'group3' =>
    array (
      'symbol' => '+',
      'value' =>
      array (
        'deep' =>
        array (
          'id' =>
          array (
            'number' => 45,
          ),
        ),
        'fee' => 100500,
      ),
    ),
);

$hi = [
    "settings6" => [
        "key" => true,
        "doge" => [
            "wow" => ""
        ]
    ],
  ];

$result = makeJson($comp);
$encode = json_encode($hi, 0, 512);
echo $encode;
$decode = json_decode($result, true);
// var_dump($decode);
