<?php

namespace Differ\Formatters\Json;

use function Differ\Differ\getNode;

function format(array $comparedArray): string
{
    return json_encode($comparedArray, JSON_PRETTY_PRINT);
}
