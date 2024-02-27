<?php

namespace Differ\Formatters;

function chooseFormateAndPrint(string $format, array $differencies): string
{
    switch ($format) {
        case 'stylish':
            return Stylish\format($differencies);
        case 'plain':
            return Plain\format($differencies);
        case 'json':
            return Json\format($differencies);
        default:
            throw new \Exception("Unknown report format: \"{$format}\"!");
    }
}
