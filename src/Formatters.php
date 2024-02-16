<?php

namespace Differ\Formatters;

use function Differ\Formatters\plain\showPlain;
use function Differ\Formatters\Stylish\makeArrayFromDifferencies;
use function Differ\Formatters\Json\printJson;

function chooseFormateAndPrint(string $format, array $differencies)
{
    switch ($format) {
        case 'stylish':
            // $result = makeArrayFromDifferencies($differencies);
            return makeArrayFromDifferencies($differencies);
        case 'plain':
            return showPlain($differencies);
        case 'json':
            return printJson($differencies);
        //     if ($resultJson === false) {
        //         throw new \Exception("Failed to turn array into string");
        //     }
        //     return str_replace("\"  ", "\"", $resultJson);
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
