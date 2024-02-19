<?php

namespace Differ\Formatters;

use Differ\Formatters;

function chooseFormateAndPrint(string $format, array $differencies)
{
    switch ($format) {
        case 'stylish':
            return Stylish\makeStylish($differencies);
        case 'plain':
            return Plain\showPlain($differencies);
        case 'json':
            return Json\printJson($differencies);
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
