<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseFile(string $extention, string $content)
{
    return match ($extention) {
        'json' => json_decode($content, true),
        'yaml', 'yml' => Yaml::parse($content),
        default => throw new \Exception("Unknow file extention: \"{$extention}\"!"),
    };
}
