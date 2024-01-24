<?php

namespace Differ\Formatters;

use function Differ\Differ\makeArrayFromDifferencies;
use function Differ\Formatters\plain\showPlain;
use function Differ\Formatters\Stylish\printing;

function chooseFormate(string $format, array $array)
{
    switch ($format) {
        case 'stylish':
            $result = makeArrayFromDifferencies($array);
            return printing($result);
        case 'plain':
            return showPlain($array);
        case 'json':
            $result = makeArrayFromDifferencies($array);
            return str_replace("\"  ", "\"", json_encode($result, JSON_PRETTY_PRINT));
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
