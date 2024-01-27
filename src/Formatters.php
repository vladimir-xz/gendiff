<?php

namespace Differ\Formatters;

use function Differ\Differ\makeArrayFromDifferencies;
use function Differ\Formatters\plain\showPlain;
use function Differ\Formatters\Stylish\stylishPrinting;

function chooseFormateAndPrint(string $format, array $differencies)
{
    switch ($format) {
        case 'stylish':
            $result = makeArrayFromDifferencies($differencies);
            return stylishPrinting($result);
        case 'plain':
            return showPlain($differencies);
        case 'json':
            $result = makeArrayFromDifferencies($differencies);
            $resultJson = json_encode($result, JSON_PRETTY_PRINT);
            if ($resultJson === false) {
                throw new \Exception("Failed to turn array into string");
            }
            return str_replace("\"  ", "\"", $resultJson);
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
