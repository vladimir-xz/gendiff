<?php

namespace Differ\Formatters;

use function Differ\Formatters\plain\showPlain;
use function Differ\Formatters\Stylish\showStylish;
use function Differ\Formatters\json\showJson;

function chooseFormate(string $format, array $array)
{
    switch ($format) {
        case 'stylish':
            return showStylish($array);
        case 'plain':
            return showPlain($array);
        case 'json':
            return json_encode($array);
    }
}
