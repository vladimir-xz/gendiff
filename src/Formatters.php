<?php

namespace Differ\Differ;

use Differ\Formatters;

function chooseFormateAndPrint(string $format, array $differencies)
{
    switch ($format) {
        case 'stylish':
            return makeStylish($differencies);
        case 'plain':
            return showPlain($differencies);
        case 'json':
            return printJson($differencies);
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
