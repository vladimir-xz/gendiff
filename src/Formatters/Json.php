<?php

namespace Differ\Formatters\Json;

function format(array $comparedArray): string
{
    return json_encode($comparedArray, JSON_PRETTY_PRINT);
}
