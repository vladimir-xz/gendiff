<?php

namespace Differ\Formatters;

use function Differ\Differ\makeArrayOfDifferencies;
use function Differ\Formatters\plain\showPlain;
use function Differ\Formatters\Stylish\printing;
use function Differ\Formatters\json\showJson;

function chooseFormate(string $format, array $array)
{
    switch ($format) {
        case 'stylish':
            $result = makeArrayOfDifferencies($array);
            return printing($result);
        case 'plain':
            return showPlain($array);
        case 'json':
            $result = makeArrayOfDifferencies($array);
            return json_encode($result, JSON_PRETTY_PRINT);
    }
}
