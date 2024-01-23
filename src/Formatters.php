<?php

namespace Differ\Formatters;

use function Differ\Formatters\plain\showPlain;
use function Differ\Formatters\Stylish\showStylish;

function chooseFormate(string $format, array $array)
{
    switch ($format) {
        case 'stylish':
            return showStylish($array);
        case 'plain':
            return showPlain($array);
    }
}
