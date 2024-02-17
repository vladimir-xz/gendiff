<?php

namespace Differ\Formatters;

use function Differ\Formatters\Plain\showPlain;
use function Differ\Formatters\Stylish\makeStringUsingInterfaces;
use function Differ\Formatters\Json\printJson;

function chooseFormateAndPrint(string $format, array $differencies)
{
    switch ($format) {
        case 'stylish':
            return makeStringUsingInterfaces($differencies);
        case 'plain':
            return showPlain($differencies);
        case 'json':
            return printJson($differencies);
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
